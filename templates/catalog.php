<div class="mt-2 mb-2">
    <h1>Notre Catalogue</h1>
    <p>Découvrez notre sélection de livres.</p>
</div>

<div class="grid">
    <?php foreach ($books as $book): ?>
        <div class="card">
            <?php if ($book->image_url): ?>
                <img src="<?= htmlspecialchars($book->image_url) ?>" alt="<?= htmlspecialchars($book->title) ?>" class="card-img">
            <?php else: ?>
                <div class="card-img" style="background-color: #eee; display: flex; align-items: center; justify-content: center;">Pas d'image</div>
            <?php endif; ?>
            
            <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($book->title) ?></h3>
                <p class="card-author">par <?= htmlspecialchars($book->author) ?></p>
                <p><?= htmlspecialchars(substr($book->description, 0, 100)) ?>...</p>
                
                <div class="card-price"><?= number_format($book->price, 0, ',', ' ') ?> FCFA</div>
                
                <?php if ($book->stock > 0): ?>
                    <div class="stock-badge stock-available">
                        ✓ En stock (<?= $book->stock ?> disponibles)
                    </div>
                    <form action="index.php?action=add" method="POST">
                        <input type="hidden" name="book_id" value="<?= $book->id ?>">
                        <button type="submit" class="btn btn-block">Ajouter au panier</button>
                    </form>
                <?php else: ?>
                    <div class="stock-badge stock-out">
                        ✗ Rupture de stock
                    </div>
                    <button class="btn btn-block" disabled style="opacity: 0.5; cursor: not-allowed;">Non disponible</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="devis-section">
    <h2>Besoin d'une commande spécifique ?</h2>
    <p>Envoyez-nous votre liste de fournitures (photo ou texte) et recevez un devis express.</p>
    <div class="devis-buttons">
        <a href="https://wa.me/221770000000" target="_blank" class="btn btn-success" style="background-color: #25D366;">
            Via WhatsApp
        </a>
        <a href="mailto:contact@bookstore.com?subject=Demande de devis" class="btn" style="background-color: #EA4335;">
            Via Email
        </a>
    </div>
</div>
