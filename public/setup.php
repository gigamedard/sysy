<?php

require_once __DIR__ . '/../config/db.php';

// Read config manually since we might not have the DB created yet
$config = require __DIR__ . '/../config/db.php';

try {
    // Connect to MySQL server without selecting a database
    $pdo = new PDO(
        "mysql:host={$config['host']};charset={$config['charset']}",
        $config['user'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database if it doesn't exist
    echo "Création de la base de données '{$config['dbname']}'...<br>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['dbname']}`");
    
    // Select the database
    $pdo->exec("USE `{$config['dbname']}`");
    echo "Base de données sélectionnée.<br>";

    // Create tables manually to be sure
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS books (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            image_url VARCHAR(255),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'books' vérifiée.<br>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            delivery_address VARCHAR(500) NOT NULL,
            payment_method ENUM('wave', 'om', 'cod') NOT NULL,
            total_amount DECIMAL(10, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'orders' vérifiée.<br>";

    // Check if delivery_address column exists (migration for existing databases)
    $columns = $pdo->query("SHOW COLUMNS FROM orders LIKE 'delivery_address'")->fetchAll();
    if (empty($columns)) {
        echo "Ajout de la colonne 'delivery_address'...<br>";
        $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_address VARCHAR(500) NOT NULL AFTER customer_phone");
        echo "Colonne 'delivery_address' ajoutée avec succès.<br>";
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            book_id INT NOT NULL,
            quantity INT NOT NULL,
            price_at_purchase DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id)
        )
    ");
    echo "Table 'order_items' vérifiée.<br>";

    // Check if books exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM books");
    if ($stmt->fetchColumn() == 0) {
        echo "Insertion des livres...<br>";
        $stmt = $pdo->prepare("INSERT INTO books (title, author, price, image_url, description) VALUES (?, ?, ?, ?, ?)");
        
        $books = [
            ['Le Petit Prince', 'Antoine de Saint-Exupéry', 5000.00, 'https://covers.openlibrary.org/b/id/12605387-L.jpg', 'Un classique de la littérature française.'],
            ['L\'Étranger', 'Albert Camus', 4500.00, 'https://covers.openlibrary.org/b/id/12653056-L.jpg', 'Un roman philosophique.'],
            ['Une si longue lettre', 'Mariama Bâ', 3500.00, 'https://covers.openlibrary.org/b/id/10584307-L.jpg', 'Un roman épistolaire majeur de la littérature africaine.'],
            ['Harry Potter à l\'école des sorciers', 'J.K. Rowling', 8000.00, 'https://covers.openlibrary.org/b/id/10522666-L.jpg', 'Le début de la saga magique.'],
            ['Clean Code', 'Robert C. Martin', 25000.00, 'https://covers.openlibrary.org/b/id/12539642-L.jpg', 'Le guide pour écrire du code propre.']
        ];

        foreach ($books as $book) {
            $stmt->execute($book);
        }
        echo "Livres insérés avec succès.<br>";
    } else {
        echo "Les livres existent déjà.<br>";
    }
    
    echo "✅ Installation terminée avec succès ! <a href='index.php'>Aller à l'accueil</a>";

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
