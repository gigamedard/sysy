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
        // Debugging
        if (empty($books)) {
            echo "<!-- DEBUG: Aucune donnée trouvée dans la table books -->";
            // Check if table exists
            try {
                $check = $this->pdo->query("SELECT COUNT(*) FROM books");
                echo "<!-- DEBUG: Nombre de livres en base : " . $check->fetchColumn() . " -->";
            } catch (\Exception $e) {
                echo "<!-- DEBUG: Erreur SQL : " . $e->getMessage() . " -->";
            }
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

    public function create(string $title, string $author, float $price, string $imageUrl, string $description): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO books (title, author, price, stock, image_url, description) 
            VALUES (:title, :author, :price, 0, :image_url, :description)
        ");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'price' => $price,
            'image_url' => $imageUrl,
            'description' => $description
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $author, float $price, string $imageUrl, string $description): bool {
        $stmt = $this->pdo->prepare("
            UPDATE books 
            SET title = :title, author = :author, price = :price, 
                image_url = :image_url, description = :description 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'price' => $price,
            'image_url' => $imageUrl,
            'description' => $description
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function updateStock(int $id, int $quantity): bool {
        $stmt = $this->pdo->prepare("UPDATE books SET stock = stock + :quantity WHERE id = :id");
        return $stmt->execute(['id' => $id, 'quantity' => $quantity]);
    }

    public function decrementStock(int $id, int $quantity): bool {
        // Check if enough stock
        $book = $this->find($id);
        if (!$book || $book->stock < $quantity) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("UPDATE books SET stock = stock - :quantity WHERE id = :id");
        return $stmt->execute(['id' => $id, 'quantity' => $quantity]);
    }

    public function hasStock(int $id, int $quantity = 1): bool {
        $book = $this->find($id);
        return $book && $book->stock >= $quantity;
    }
}
