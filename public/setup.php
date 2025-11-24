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

    // Read schema.sql
    $sql = file_get_contents(__DIR__ . '/../schema.sql');
    
    // Execute schema
    echo "Importation des tables...<br>";
    // Execute schema
    echo "Importation des tables...<br>";
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    // Split by semicolon
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    
    echo "✅ Installation terminée avec succès ! <a href='index.php'>Aller à l'accueil</a>";

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
