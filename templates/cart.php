<h1>Votre Panier</h1>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
        Votre panier est vide. <a href="index.php">Retourner au catalogue</a>.
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Livre</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $bookId => $quantity): ?>
                <?php $book = $bookRepo->find($bookId); ?>
                <?php if ($book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book->title) ?></td>
                        <td><?= number_format($book->price, 0, ',', ' ') ?> FCFA</td>
                        <td><?= $quantity ?></td>
                        <td><?= number_format($book->price * $quantity, 0, ',', ' ') ?> FCFA</td>
                        <td>
                            <a href="index.php?action=remove&id=<?= $bookId ?>" class="btn btn-danger btn-sm">Retirer</a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
                <td colspan="2" style="font-weight: bold; color: var(--accent-color); font-size: 1.2rem;">
                    <?= number_format($total, 0, ',', ' ') ?> FCFA
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="flex-between">
        <a href="index.php?action=clear" class="btn btn-danger">Vider le panier</a>
        <div>
            <a href="index.php" class="btn">Continuer mes achats</a>
            <a href="index.php?page=checkout" class="btn btn-accent">Commander</a>
        </div>
    </div>
<?php endif; ?>
