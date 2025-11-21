<?php

namespace Bookstore;

class Book {
    public function __construct(
        public ?int $id,
        public string $title,
        public string $author,
        public float $price,
        public ?string $image_url,
        public ?string $description
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['id'] ?? null,
            $data['title'],
            $data['author'],
            (float)$data['price'],
            $data['image_url'] ?? null,
            $data['description'] ?? null
        );
    }
}
