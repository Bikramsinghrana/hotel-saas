<?php

namespace App\Repositories;

interface PaymentRepositoryInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
    public function findByOrderNumber(string $orderNumber);
}
