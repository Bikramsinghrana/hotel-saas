<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentServiceInterface;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected $service;

    public function __construct(PaymentServiceInterface $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('Stripe-Signature');

        try {
            $this->service->handleWebhook($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook handling failed'], 400);
        }

        return response()->json(['ok' => true]);
    }
}
