<?php

namespace Bookstore;

use PDO;
use Exception;

class OrderService {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function createOrder(string $name, string $phone, string $paymentMethod, Cart $cart): int {
        if ($cart->getCount() === 0) {
            throw new Exception("Le panier est vide.");
        }

        $this->pdo->beginTransaction();

        try {
            // 1. Create Order
            $bookRepo = new BookRepository();
            $total = $cart->getTotal($bookRepo);

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (customer_name, customer_phone, payment_method, total_amount)
                VALUES (:name, :phone, :payment, :total)
            ");
            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'payment' => $paymentMethod,
                'total' => $total
            ]);
            
            $orderId = (int)$this->pdo->lastInsertId();

            // 2. Create Order Items
            $stmtItem = $this->pdo->prepare("
                INSERT INTO order_items (order_id, book_id, quantity, price_at_purchase)
                VALUES (:order_id, :book_id, :quantity, :price)
            ");

            foreach ($cart->getItems() as $bookId => $quantity) {
                $book = $bookRepo->find($bookId);
                if ($book) {
                    $stmtItem->execute([
                        'order_id' => $orderId,
                        'book_id' => $bookId,
                        'quantity' => $quantity,
                        'price' => $book->price
                    ]);
                }
            }

            $this->pdo->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
