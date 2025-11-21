<?php

namespace Bookstore;

use PDO;

class BookRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM books ORDER BY created_at DESC");
        $books = [];
        while ($row = $stmt->fetch()) {
            $books[] = Book::fromArray($row);
        }
        return $books;
    }

    public function find(int $id): ?Book {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return Book::fromArray($row);
        }
        return null;
    }
}
