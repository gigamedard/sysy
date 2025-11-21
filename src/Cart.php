<?php

namespace Bookstore;

class Cart {
    private array $items = [];

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->items = $_SESSION['cart'] ?? [];
    }

    public function add(int $bookId, int $quantity = 1): void {
        if (isset($this->items[$bookId])) {
            $this->items[$bookId] += $quantity;
        } else {
            $this->items[$bookId] = $quantity;
        }
        $this->save();
    }

    public function remove(int $bookId): void {
        if (isset($this->items[$bookId])) {
            unset($this->items[$bookId]);
            $this->save();
        }
    }

    public function update(int $bookId, int $quantity): void {
        if ($quantity <= 0) {
            $this->remove($bookId);
            return;
        }
        $this->items[$bookId] = $quantity;
        $this->save();
    }

    public function getItems(): array {
        return $this->items;
    }

    public function clear(): void {
        $this->items = [];
        $this->save();
    }

    public function getTotal(BookRepository $bookRepo): float {
        $total = 0;
        foreach ($this->items as $bookId => $quantity) {
            $book = $bookRepo->find($bookId);
            if ($book) {
                $total += $book->price * $quantity;
            }
        }
        return $total;
    }

    public function getCount(): int {
        return array_sum($this->items);
    }

    private function save(): void {
        $_SESSION['cart'] = $this->items;
    }
}
