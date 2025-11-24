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
}
