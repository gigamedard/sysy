<?php

namespace Bookstore;

class Order {
    public function __construct(
        public ?int $id,
        public string $customerName,
        public string $customerPhone,
        public string $paymentMethod,
        public float $totalAmount,
        public ?string $createdAt = null
    ) {}
}
