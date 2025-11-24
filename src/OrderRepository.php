<?php

namespace Bookstore;

use PDO;

class OrderRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Get all orders with basic info, ordered by most recent first
     */
    public function findAll(): array {
        $stmt = $this->pdo->query("
            SELECT * FROM orders 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single order by ID
     */
    public function find(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    /**
     * Get all items for a specific order
     */
    public function findOrderItems(int $orderId): array {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, b.title, b.author 
            FROM order_items oi
            JOIN books b ON oi.book_id = b.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
