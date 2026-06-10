<?php

namespace App\Services;

use App\Repositories\PaymentRepositoryInterface;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingPaid;

class PaymentService implements PaymentServiceInterface
{
    protected $payments;

    public function __construct(PaymentRepositoryInterface $payments)
    {
        $this->payments = $payments;
    }

    public function createCheckoutSession($order, string $successUrl, string $cancelUrl)
    {
        // Create a payment record with pending status
        $payment = $this->payments->create([
            'tenant_id' => $order->tenant_id ?? null,
            'room_order_id' => $order->id,
            'order_number' => $order->order_number ?? null,
            'amount' => $order->total_amount,
            'currency' => $order->currency ?? 'USD',
            'payment_method' => 'card',
            'gateway' => 'stripe',
            'status' => 'pending',
        ]);

        // Build Stripe checkout session
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            throw new \RuntimeException('Stripe secret not configured.');
        }

        $stripe = new \Stripe\StripeClient($stripeSecret);

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($payment->currency),
                    'product_data' => ['name' => 'Booking: ' . ($order->order_number ?? $order->id)],
                    'unit_amount' => intval(round($payment->amount * 100)),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
            ],
        ]);

        // Store session id as transaction id until confirmed
        $this->payments->update($payment->id, ['transaction_id' => $session->id]);

        return $session;
    }

    public function createPaymentIntent($order)
    {
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            throw new \RuntimeException('Stripe secret not configured.');
        }

        $stripe = new \Stripe\StripeClient($stripeSecret);

        $amount = intval(round(($order->total_amount ?? 0) * 100));

        $intent = $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => strtolower($order->currency ?? 'USD'),
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        return [
            'client_secret' => $intent->client_secret ?? ($intent['client_secret'] ?? null),
            'intent_id' => $intent->id ?? ($intent['id'] ?? null),
        ];
    }

    public function handleWebhook(array $payload, ?string $signature = null): void
    {
        $endpointSecret = config('services.stripe.webhook_secret');
        try {
            if ($endpointSecret && $signature) {
                $event = \Stripe\Webhook::constructEvent(json_encode($payload), $signature, $endpointSecret);
            } else {
                $event = (object) $payload;
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        $type = is_object($event) ? ($event->type ?? null) : ($event['type'] ?? null);
        $data = is_object($event) ? ($event->data->object ?? null) : ($event['data']['object'] ?? null);

        if ($type === 'checkout.session.completed' && $data) {
            $session = $data;
            $paymentId = $session->metadata->payment_id ?? ($session['metadata']['payment_id'] ?? null);
            $transactionId = $session->payment_intent ?? ($session['payment_intent'] ?? null);

            if ($paymentId) {
                try {
                    $this->payments->update($paymentId, [
                        'status' => 'paid',
                        'transaction_id' => $transactionId,
                        'payment_response' => json_encode($session),
                        'paid_at' => now(),
                    ]);

                    $payment = \App\Models\Payment::find($paymentId);
                    if ($payment) {
                        // create invoice
                        $this->createInvoiceForPayment($payment);

                        // update related order status
                        $order = $payment->order;
                        if ($order) {
                            $order->update(['status' => 'confirmed', 'payment_status' => 'paid']);
                            // send booking confirmation email with invoice
                            try {
                                $invoice = \App\Models\Invoice::where('payment_id', $payment->id)->first();
                                if ($order->email) {
                                    Mail::to($order->email)->send(new BookingPaid($order, $invoice));
                                }
                            } catch (\Exception $e) {
                                Log::warning('Failed to send booking paid email', ['error' => $e->getMessage()]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update payment after webhook', ['error' => $e->getMessage()]);
                }
            }
        }

        // Handle PaymentIntent succeeded events for Elements flows
        if ($type === 'payment_intent.succeeded' && $data) {
            $intent = $data;
            $orderId = $intent->metadata->order_id ?? ($intent['metadata']['order_id'] ?? null);
            $transactionId = $intent->id ?? ($intent['id'] ?? null);

            if ($orderId) {
                try {
                    // create payment record linked to order
                    $order = \App\Models\RoomOrder::find($orderId);
                    if ($order) {
                        $payment = \App\Models\Payment::create([
                            'tenant_id' => $order->tenant_id ?? null,
                            'room_order_id' => $order->id,
                            'order_number' => $order->order_number ?? null,
                            'amount' => $order->total_amount,
                            'currency' => $order->currency ?? 'USD',
                            'payment_method' => 'card',
                            'gateway' => 'stripe',
                            'status' => 'paid',
                            'transaction_id' => $transactionId,
                            'payment_response' => json_encode($intent),
                            'paid_at' => now(),
                        ]);

                        // create invoice and update order
                        $this->createInvoiceForPayment($payment);
                        $order->update(['status' => 'confirmed', 'payment_status' => 'paid']);

                        // send email
                        try {
                            $invoice = \App\Models\Invoice::where('payment_id', $payment->id)->first();
                            if ($order->email) {
                                Mail::to($order->email)->send(new BookingPaid($order, $invoice));
                            }
                        } catch (\Exception $e) {
                            Log::warning('Failed to send booking paid email (intent flow)', ['error' => $e->getMessage()]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to process payment_intent.succeeded webhook', ['error' => $e->getMessage()]);
                }
            }
        }

        if ($type === 'checkout.session.expired' && $data) {
            $paymentId = $data->metadata->payment_id ?? ($data['metadata']['payment_id'] ?? null);
            if ($paymentId) {
                $this->payments->update($paymentId, ['status' => 'failed']);
            }
        }
    }

    public function createInvoiceForPayment($payment, array $data = [])
    {
        // generate invoice number
        $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

        $order = $payment->order;

        $invoice = Invoice::create([
            'tenant_id' => $payment->tenant_id ?? null,
            'room_order_id' => $payment->room_order_id,
            'payment_id' => $payment->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $payment->amount,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'total_amount' => $payment->amount + ($data['tax_amount'] ?? 0),
            'issued_at' => now(),
        ]);

        // try to generate pdf using barryvdh/laravel-dompdf if available
        try {
            if (class_exists(\PDF::class)) {
                $pdf = \PDF::loadView('invoices.template', compact('invoice', 'payment', 'order'));
                $path = 'invoices/' . $invoice->invoice_number . '.pdf';
                \Storage::disk('public')->put($path, $pdf->output());
                $invoice->update(['pdf_path' => $path]);
            }
        } catch (\Exception $e) {
            Log::warning('Invoice PDF generation failed', ['error' => $e->getMessage()]);
        }

        return $invoice;
    }
}
