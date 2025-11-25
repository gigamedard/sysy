<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Bookstore\OrderRepository;

$orderRepo = new OrderRepository();
$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    if ($orderRepo->updateStatus($orderId, $newStatus)) {
        $message = "Statut mis à jour avec succès.";
    }
}

// Get all orders
$orders = $orderRepo->findAll();

// Get order details if requested
$orderDetails = null;
if (isset($_GET['order_id'])) {
    $orderDetails = $orderRepo->find((int)$_GET['order_id']);
    if ($orderDetails) {
        $orderDetails['items'] = $orderRepo->findOrderItems((int)$_GET['order_id']);
    }
}

// Status labels and styles
$statusLabels = [
    'pending' => 'En attente de paiement',
    'paid' => 'Payé / En cours de livraison',
    'delivered' => 'Livré'
];

$statusClasses = [
    'pending' => 'status-pending',
    'paid' => 'status-paid',
    'delivered' => 'status-delivered'
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Commandes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .order-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.2s;
        }
        .order-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .order-id {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .order-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        .meta-value {
            font-weight: 600;
            color: #111827;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: var(--primary-color);
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>📊 Administration - Commandes</h1>
                <p>Gestion des commandes client</p>
            </div>
            <a href="logout.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid white;">
                🚪 Déconnexion
            </a>
        </div>
    </div>

    <main class="container">
        <div class="admin-nav" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <a href="admin.php" style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">📊 Commandes</a>
            <a href="admin-books.php" style="padding: 0.75rem 1.5rem; background: white; color: var(--primary-color); text-decoration: none; border-radius: 8px; font-weight: 600; border: 2px solid var(--primary-color);">📚 Livres</a>
        </div>
        <?php if ($orderDetails): ?>
            <!-- Order Details View -->
            <a href="admin.php" class="back-link">← Retour à la liste</a>
            
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Commande #<?= $orderDetails['id'] ?></div>
                        <small style="color: #6b7280;">
                            <?= date('d/m/Y à H:i', strtotime($orderDetails['created_at'])) ?>
                        </small>
                    </div>
                    <span class="status-badge status-pending">En attente</span>
                </div>

                <div class="order-meta">
                    <div class="meta-item">
                        <span class="meta-label">Client</span>
                        <span class="meta-value"><?= htmlspecialchars($orderDetails['customer_name']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Téléphone</span>
                        <span class="meta-value"><?= htmlspecialchars($orderDetails['customer_phone']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Adresse de livraison</span>
                        <span class="meta-value"><?= htmlspecialchars($orderDetails['delivery_address']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Paiement</span>
                        <span class="meta-value">
                            <?php
                            $methods = [
                                'wave' => 'Wave',
                                'om' => 'Orange Money',
                                'cod' => 'Paiement à la livraison'
                            ];
                            echo $methods[$orderDetails['payment_method']] ?? $orderDetails['payment_method'];
                            ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Montant total</span>
                        <span class="meta-value" style="color: var(--accent-color); font-size: 1.25rem;">
                            <?= number_format($orderDetails['total_amount'], 0, ',', ' ') ?> FCFA
                        </span>
                    </div>
                </div>

                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Articles commandés</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livre</th>
                            <th>Auteur</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td><?= htmlspecialchars($item['author']) ?></td>
                                <td><?= number_format($item['price_at_purchase'], 0, ',', ' ') ?> FCFA</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['price_at_purchase'] * $item['quantity'], 0, ',', ' ') ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <!-- Orders List View -->
            <h2>Liste des commandes (<?= count($orders) ?>)</h2>
            
            <?php if (empty($orders)): ?>
                <div class="alert alert-info">
                    Aucune commande pour le moment.
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <a href="admin.php?order_id=<?= $order['id'] ?>" class="order-id">
                                    Commande #<?= $order['id'] ?>
                                </a>
                                <small style="color: #6b7280; display: block; margin-top: 0.25rem;">
                                    <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?>
                                </small>
                            </div>
                            <span class="status-badge <?= $statusClasses[$order['status']] ?? '' ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </div>

                        <div class="order-meta">
                            <div class="meta-item">
                                <span class="meta-label">Client</span>
                                <span class="meta-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Téléphone</span>
                                <span class="meta-value"><?= htmlspecialchars($order['customer_phone']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Paiement</span>
                                <span class="meta-value">
                                    <?php
                                    $methods = [
                                        'wave' => 'Wave',
                                        'om' => 'Orange Money',
                                        'cod' => 'À la livraison'
                                    ];
                                    echo $methods[$order['payment_method']] ?? $order['payment_method'];
                                    ?>
                                </span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Montant</span>
                                <span class="meta-value" style="color: var(--accent-color);">
                                    <?= number_format($order['total_amount'], 0, ',', ' ') ?> FCFA
                                </span>
                            </div>
                        </div>

                        <a href="admin.php?order_id=<?= $order['id'] ?>" class="btn btn-sm" style="margin-top: 1rem;">
                            Voir les détails →
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer style="margin-top: 4rem; padding: 2rem; background-color: var(--dark-bg); color: white; text-align: center;">
        <p>© <?= date('Y') ?> Bookstore. Tous droits réservés.</p>
    </footer>
</body>
</html>
