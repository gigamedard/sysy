<div class="text-center mt-2">
    <div style="font-size: 4rem; color: var(--success-color);">✅</div>
    <h1>Commande Reçue !</h1>
    <p>Merci pour votre commande.</p>
    <?php if (isset($_GET['order'])): ?>
        <p>Numéro de commande : <strong>#<?= htmlspecialchars($_GET['order']) ?></strong></p>
    <?php endif; ?>
    <p>Nous vous contacterons bientôt pour la livraison.</p>
    
    <div class="mt-2">
        <a href="/" class="btn">Retour à l'accueil</a>
    </div>
</div>
