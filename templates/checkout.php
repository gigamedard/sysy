<h1>Finaliser la Commande</h1>

<div class="grid" style="grid-template-columns: 2fr 1fr;">
    <div>
        <div class="card">
            <div class="card-body">
                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/index.php?action=checkout" method="POST">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="Ex: 77 000 00 00" required>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Moyen de Paiement</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Choisir...</option>
                            <option value="wave" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'wave') ? 'selected' : '' ?>>Wave</option>
                            <option value="om" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'om') ? 'selected' : '' ?>>Orange Money</option>
                            <option value="cod" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cod') ? 'selected' : '' ?>>Paiement à la livraison</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-accent btn-block">Confirmer la commande</button>
                </form>
            </div>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-body">
                <h3>Récapitulatif</h3>
                <p>Total à payer :</p>
                <div class="card-price"><?= number_format($total, 0, ',', ' ') ?> FCFA</div>
                <p><small>Livraison non incluse</small></p>
            </div>
        </div>
    </div>
</div>
