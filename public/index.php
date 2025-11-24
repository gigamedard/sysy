<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bookstore\BookRepository;
use Bookstore\Cart;
use Bookstore\OrderService;
use Bookstore\Validator;

session_start();

$page = $_GET['page'] ?? 'catalog';
$action = $_GET['action'] ?? null;

$bookRepo = new BookRepository();
$cart = new Cart();

// Handle Actions
if ($action) {
    if ($action === 'add' && isset($_POST['book_id'])) {
        $cart->add((int)$_POST['book_id']);
        header('Location: index.php?page=cart');
        exit;
    }
    
    if ($action === 'remove' && isset($_GET['id'])) {
        $cart->remove((int)$_GET['id']);
        header('Location: index.php?page=cart');
        exit;
    }

    if ($action === 'clear') {
        $cart->clear();
        header('Location: index.php?page=cart');
        exit;
    }

    if ($action === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $validator = new Validator();
        if ($validator->validateOrder($_POST)) {
            try {
                $orderService = new OrderService();
                $orderId = $orderService->createOrder(
                    $_POST['name'],
                    $_POST['phone'],
                    $_POST['payment_method'],
                    $cart
                );
                $cart->clear();
                header("Location: index.php?page=success&order=$orderId");
                exit;
            } catch (Exception $e) {
                $error = "Erreur lors de la commande : " . $e->getMessage();
            }
        } else {
            $errors = $validator->getErrors();
            $page = 'checkout'; // Stay on checkout page
        }
    }
}

// Routing
require_once __DIR__ . '/../templates/header.php';

switch ($page) {
    case 'catalog':
        $books = $bookRepo->findAll();
        require_once __DIR__ . '/../templates/catalog.php';
        break;
    
    case 'cart':
        $cartItems = $cart->getItems();
        $total = $cart->getTotal($bookRepo);
        require_once __DIR__ . '/../templates/cart.php';
        break;
    
    case 'checkout':
        $total = $cart->getTotal($bookRepo);
        if ($cart->getCount() === 0) {
            header('Location: index.php');
            exit;
        }
        require_once __DIR__ . '/../templates/checkout.php';
        break;

    case 'success':
        require_once __DIR__ . '/../templates/success.php';
        break;

    default:
        echo "<h1>Page non trouvée</h1>";
        break;
}

require_once __DIR__ . '/../templates/footer.php';
