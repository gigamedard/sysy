<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Bookstore\Cart;
use Bookstore\BookRepository;
use Bookstore\OrderService;

// Check if payment info is in session
if (!isset($_SESSION['pending_order'])) {
    header('Location: index.php');
    exit;
}

$orderData = $_SESSION['pending_order'];
$paymentMethod = $orderData['payment_method'];

// Only Wave and Orange Money go through this page
if (!in_array($paymentMethod, ['wave', 'om'])) {
    header('Location: index.php');
    exit;
}

$cart = new Cart();
$bookRepo = new BookRepository();
$total = $cart->getTotal($bookRepo);

// Payment colors based on provider
$colors = [
    'wave' => ['primary' => '#1e88e5', 'gradient' => 'linear-gradient(135deg, #1e88e5 0%, #1565c0 100%)'],
    'om' => ['primary' => '#ff6d00', 'gradient' => 'linear-gradient(135deg, #ff6d00 0%, #e65100 100%)']
];

$logos = [
    'wave' => '📱 Wave',
    'om' => '🍊 Orange Money'
];

$color = $colors[$paymentMethod];
$logo = $logos[$paymentMethod];

// Handle payment confirmation
$paymentConfirmed = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $phoneNumber = $_POST['payment_phone'] ?? '';
    
    if (!empty($phoneNumber)) {
        try {
            // Create the order
            $orderService = new OrderService();
            $orderId = $orderService->createOrder(
                $orderData['name'],
                $orderData['phone'],
                $orderData['delivery_address'],
                $orderData['payment_method'],
                $cart
            );
            
            // Clear cart and session
            $cart->clear();
            unset($_SESSION['pending_order']);
            
            // Show confirmation then redirect
            $paymentConfirmed = true;
            $confirmationOrderId = $orderId;
            
        } catch (Exception $e) {
            $error = "Erreur lors du paiement : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez entrer votre numéro de téléphone.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - <?= $logo ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: <?= $color['gradient'] ?>;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .payment-container {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }
        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .payment-logo {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .payment-title {
            color: <?= $color['primary'] ?>;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .payment-amount {
            background: #f3f4f6;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .payment-amount-label {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .payment-amount-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: <?= $color['primary'] ?>;
        }
        .payment-form {
            margin-bottom: 2rem;
        }
        .payment-form .form-group {
            margin-bottom: 1.5rem;
        }
        .payment-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        .payment-form input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1.1rem;
            text-align: center;
            letter-spacing: 1px;
        }
        .payment-form input:focus {
            outline: none;
            border-color: <?= $color['primary'] ?>;
        }
        .payment-btn {
            width: 100%;
            padding: 1rem;
            background: <?= $color['primary'] ?>;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .payment-info {
            background: #fef3c7;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        .order-summary {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .order-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #6b7280;
            text-decoration: none;
        }
        .cancel-link:hover {
            color: #374151;
            text-decoration: underline;
        }
        /* Confirmation styles */
        .confirmation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .confirmation-box {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .confirmation-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 1s;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }
        .confirmation-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 0.5rem;
        }
        .confirmation-message {
            color: #6b7280;
            margin-bottom: 1rem;
        }
        .sms-simulation {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1.5rem;
            text-align: left;
            font-family: monospace;
            font-size: 0.85rem;
            color: #374151;
            border: 2px dashed #d1d5db;
        }
    </style>
</head>
<body>
    <?php if ($paymentConfirmed): ?>
        <div class="confirmation-overlay">
            <div class="confirmation-box">
                <div class="confirmation-icon">✅</div>
                <div class="confirmation-title">Paiement Réussi !</div>
                <div class="confirmation-message">
                    Votre commande #<?= $confirmationOrderId ?> a été confirmée.
                </div>
                
                <div class="sms-simulation">
                    📱 <strong>SMS de confirmation</strong><br><br>
                    Paiement de <?= number_format($total, 0, ',', ' ') ?> FCFA reçu via <?= $logo ?>.<br>
                    Commande #<?= $confirmationOrderId ?><br>
                    Merci pour votre achat !<br>
                    - Bookstore
                </div>
                
                <script>
                    setTimeout(function() {
                        window.location.href = 'index.php?page=success&order=<?= $confirmationOrderId ?>';
                    }, 4000);
                </script>
            </div>
        </div>
    <?php else: ?>
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-logo"><?= $paymentMethod === 'wave' ? '📱' : '🍊' ?></div>
                <div class="payment-title"><?= $logo ?></div>
                <p style="color: #6b7280; font-size: 0.9rem;">Paiement sécurisé</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="payment-amount">
                <div class="payment-amount-label">Montant à payer</div>
                <div class="payment-amount-value"><?= number_format($total, 0, ',', ' ') ?> FCFA</div>
            </div>

            <div class="payment-info">
                💡 <strong>Simulation de paiement</strong><br>
                Entrez votre numéro et confirmez le paiement. Un SMS de confirmation sera envoyé.
            </div>

            <div class="order-summary">
                <div class="order-summary-item">
                    <span><strong>Client:</strong></span>
                    <span><?= htmlspecialchars($orderData['name']) ?></span>
                </div>
                <div class="order-summary-item">
                    <span><strong>Téléphone:</strong></span>
                    <span><?= htmlspecialchars($orderData['phone']) ?></span>
                </div>
                <div class="order-summary-item">
                    <span><strong>Livraison:</strong></span>
                    <span><?= htmlspecialchars(substr($orderData['delivery_address'], 0, 30)) ?>...</span>
                </div>
            </div>

            <form method="POST" class="payment-form">
                <div class="form-group">
                    <label for="payment_phone">Numéro <?= $logo ?></label>
                    <input 
                        type="tel" 
                        id="payment_phone" 
                        name="payment_phone" 
                        placeholder="77 123 45 67" 
                        required 
                        autofocus
                        value="<?= htmlspecialchars($orderData['phone']) ?>"
                    >
                </div>

                <button type="submit" name="confirm_payment" class="payment-btn">
                    🔒 Confirmer le paiement
                </button>
            </form>

            <a href="index.php?page=checkout" class="cancel-link">← Annuler et retourner</a>
        </div>
    <?php endif; ?>
</body>
</html>
