<?php

namespace App\Services;

interface PaymentServiceInterface
{
    public function createCheckoutSession($order, string $successUrl, string $cancelUrl);
    public function handleWebhook(array $payload, ?string $signature = null): void;
    public function createInvoiceForPayment($payment, array $data = []);
}
