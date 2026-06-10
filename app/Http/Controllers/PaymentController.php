<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PaymentRepositoryInterface;
use App\Services\PaymentServiceInterface;
use App\Models\RoomOrder;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $payments;
    protected $service;

    public function __construct(PaymentRepositoryInterface $payments, PaymentServiceInterface $service)
    {
        $this->payments = $payments;
        $this->service = $service;
    }

    public function checkout(Request $request, $orderId)
    {
        $order = RoomOrder::findOrFail($orderId);

        // ensure order is pending
        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Order already paid.');
        }

        $successUrl = route('payments.success');
        $cancelUrl = route('payments.cancel');

        try {
            $session = $this->service->createCheckoutSession($order, $successUrl, $cancelUrl);
            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Payment initialization failed.');
        }
    }

    public function success(Request $request)
    {
        // show success page; actual fulfillment is processed by webhook
        return view('payments.success', ['session_id' => $request->get('session_id')]);
    }

    public function cancel(Request $request)
    {
        return view('payments.cancel');
    }

    /**
     * Return payment status by Stripe session id (transaction_id)
     */
    public function status(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return response()->json(['error' => 'Missing session_id'], 400);
        }

        $payment = \App\Models\Payment::where('transaction_id', $sessionId)->first();
        if (!$payment) {
            return response()->json(['status' => 'not_found']);
        }

        $invoice = \App\Models\Invoice::where('payment_id', $payment->id)->first();

        return response()->json([
            'status' => $payment->status,
            'payment' => $payment,
            'invoice_id' => $invoice ? $invoice->id : null,
            'order_id' => $payment->room_order_id,
        ]);
    }

    /**
     * Return payment/order status by order id
     */
    public function orderStatus($orderId)
    {
        $order = RoomOrder::find($orderId);
        if (!$order) return response()->json(['error' => 'Order not found'], 404);

        $payment = \App\Models\Payment::where('room_order_id', $orderId)->latest()->first();
        $invoice = $payment ? \App\Models\Invoice::where('payment_id', $payment->id)->first() : null;

        return response()->json([
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'payment' => $payment,
            'invoice_id' => $invoice ? $invoice->id : null,
        ]);
    }

    public function downloadInvoice($invoiceId)
    {
        $invoice = \App\Models\Invoice::findOrFail($invoiceId);
        if ($invoice->pdf_path && \Storage::disk('public')->exists($invoice->pdf_path)) {
            return response()->download(storage_path('app/public/' . $invoice->pdf_path));
        }

        // fallback: render pdf on the fly
        if (class_exists(\PDF::class)) {
            $payment = $invoice->payment;
            $order = $invoice->order;
            $pdf = \PDF::loadView('invoices.template', compact('invoice', 'payment', 'order'));
            return $pdf->download($invoice->invoice_number . '.pdf');
        }

        abort(404);
    }
}
