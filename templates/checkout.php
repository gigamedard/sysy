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

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=checkout" method="POST">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="Ex: 77 000 00 00" required>
                    </div>

                    <div class="form-group">
                        <label for="delivery_address">Adresse de livraison</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" placeholder="Ex: Dakar, Plateau, Rue 10 x 15" required><?= htmlspecialchars($_POST['delivery_address'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Moyen de Paiement *</label>
                        <div class="payment-methods">
                            <input type="radio" id="payment_wave" name="payment_method" value="wave" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'wave') ? 'checked' : '' ?> required>
                            <label for="payment_wave" class="payment-card payment-wave">
                                <div class="payment-logo">
                                    <img src="images/payment/wave.png" alt="Wave" class="payment-logo-img">
                                </div>
                                <div class="payment-name">Wave</div>
                                <div class="payment-desc">Paiement mobile</div>
                            </label>

                            <input type="radio" id="payment_om" name="payment_method" value="om" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'om') ? 'checked' : '' ?>>
                            <label for="payment_om" class="payment-card payment-om">
                                <div class="payment-logo">
                                    <img src="images/payment/orange-money.png" alt="Orange Money" class="payment-logo-img">
                                </div>
                                <div class="payment-name">Orange Money</div>
                                <div class="payment-desc">Paiement mobile</div>
                            </label>

                            <input type="radio" id="payment_cod" name="payment_method" value="cod" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cod') ? 'checked' : '' ?>>
                            <label for="payment_cod" class="payment-card payment-cod">
                                <div class="payment-logo">💵</div>
                                <div class="payment-name">Espèces</div>
                                <div class="payment-desc">À la livraison</div>
                            </label>
                        </div>
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
