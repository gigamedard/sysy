<?php

namespace Bookstore;

class Book {
    public int $id;
    public string $title;
    public string $author;
    public float $price;
    public int $stock;
    public ?string $image_url;
    public ?string $description;

    public function __construct(int $id, string $title, string $author, float $price, int $stock = 0, ?string $image_url = null, ?string $description = null) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->price = $price;
        $this->stock = $stock;
        $this->image_url = $image_url;
        $this->description = $description;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['title'],
            $data['author'],
            (float)$data['price'],
            (int)($data['stock'] ?? 0),
            $data['image_url'] ?? null,
            $data['description'] ?? null
        );
    }
}
