<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore - Votre Librairie en Ligne</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">📚 Bookstore</a>
                <div class="nav-links">
                    <a href="index.php">Catalogue</a>
                    <a href="index.php?page=cart">
                        Panier 
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="cart-badge"><?= array_sum($_SESSION['cart']) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </nav>
        </div>
    </header>
    <main class="container">
