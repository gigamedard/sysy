<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Bookstore\BookRepository;

$bookRepo = new BookRepository();
$message = '';
$error = '';

// Handle actions
$action = $_GET['action'] ?? 'list';

// Delete book
if ($action === 'delete' && isset($_GET['id'])) {
    $bookRepo->delete((int)$_GET['id']);
    $message = "Livre supprimé avec succès.";
    $action = 'list';
}

// Add/Edit book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_book'])) {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $description = $_POST['description'] ?? '';
    $imageUrl = $_POST['existing_image'] ?? '';
    
    // Handle image upload
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/books/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = uniqid('book_') . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['book_image']['tmp_name'], $filePath)) {
                $imageUrl = 'uploads/books/' . $fileName;
            } else {
                $error = "Erreur lors de l'upload de l'image.";
            }
        } else {
            $error = "Format d'image non supporté. Utilisez JPG, PNG, GIF ou WEBP.";
        }
    }
    
    if (empty($error)) {
        if (isset($_POST['book_id']) && !empty($_POST['book_id'])) {
            // Update
            $bookRepo->update((int)$_POST['book_id'], $title, $author, $price, $imageUrl, $description);
            $message = "Livre modifié avec succès.";
        } else {
            // Create
            $bookRepo->create($title, $author, $price, $imageUrl, $description);
            $message = "Livre ajouté avec succès.";
        }
        $action = 'list';
    }
}

// Get book for editing
$editBook = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editBook = $bookRepo->find((int)$_GET['id']);
}

// Get all books
$books = $bookRepo->findAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres - Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .admin-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .admin-nav a {
            padding: 0.75rem 1.5rem;
            background: white;
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .admin-nav a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .admin-nav a.active {
            background: var(--primary-color);
            color: white;
        }
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .book-admin-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.2s;
        }
        .book-admin-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .book-admin-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .book-admin-card-body {
            padding: 1rem;
        }
        .book-admin-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .book-admin-price {
            color: var(--accent-color);
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        .book-admin-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .form-book {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            max-width: 600px;
        }
        .image-preview {
            max-width: 200px;
            margin-top: 0.5rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>📚 Gestion des Livres</h1>
                <p>Ajoutez, modifiez ou supprimez des livres</p>
            </div>
            <a href="logout.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid white;">
                🚪 Déconnexion
            </a>
        </div>
    </div>

    <main class="container">
        <div class="admin-nav">
            <a href="admin.php">📊 Commandes</a>
            <a href="admin-books.php" class="active">📚 Livres</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="form-book">
                <h2><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> un livre</h2>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($editBook): ?>
                        <input type="hidden" name="book_id" value="<?= $editBook->id ?>">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editBook->image_url) ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title">Titre *</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($editBook->title ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="author">Auteur *</label>
                        <input type="text" id="author" name="author" value="<?= htmlspecialchars($editBook->author ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Prix (FCFA) *</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?= $editBook->price ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($editBook->description ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="book_image">Image du livre</label>
                        <input type="file" id="book_image" name="book_image" accept="image/*">
                        <?php if ($editBook && $editBook->image_url): ?>
                            <img src="<?= htmlspecialchars($editBook->image_url) ?>" alt="Image actuelle" class="image-preview">
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" name="save_book" class="btn btn-accent">
                            💾 Enregistrer
                        </button>
                        <a href="admin-books.php" class="btn">Annuler</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Books List -->
            <div style="margin-bottom: 1.5rem;">
                <a href="admin-books.php?action=add" class="btn btn-accent">+ Ajouter un livre</a>
            </div>

            <div class="books-grid">
                <?php foreach ($books as $book): ?>
                    <div class="book-admin-card">
                        <?php if ($book->image_url): ?>
                            <img src="<?= htmlspecialchars($book->image_url) ?>" alt="<?= htmlspecialchars($book->title) ?>">
                        <?php else: ?>
                            <div style="height: 200px; background: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                                Pas d'image
                            </div>
                        <?php endif; ?>
                        <div class="book-admin-card-body">
                            <div class="book-admin-title"><?= htmlspecialchars($book->title) ?></div>
                            <div style="color: #6b7280; font-size: 0.9rem;">par <?= htmlspecialchars($book->author) ?></div>
                            <div class="book-admin-price"><?= number_format($book->price, 0, ',', ' ') ?> FCFA</div>
                            <div class="book-admin-actions">
                                <a href="admin-books.php?action=edit&id=<?= $book->id ?>" class="btn btn-sm">✏️ Modifier</a>
                                <a href="admin-books.php?action=delete&id=<?= $book->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">🗑️ Supprimer</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer style="margin-top: 4rem; padding: 2rem; background-color: var(--dark-bg); color: white; text-align: center;">
        <p>© <?= date('Y') ?> Bookstore. Tous droits réservés.</p>
    </footer>
</body>
</html>
