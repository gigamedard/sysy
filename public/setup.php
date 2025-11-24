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
}
