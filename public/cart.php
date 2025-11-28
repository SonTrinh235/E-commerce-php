<?php
session_start();
require_once '../config/database.php'; 

if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM PRODUCTS WHERE PRODUCTID = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $name = $product['PRO_NAME'];
            $price = (float)$product['PRO_PRICE'];
            $image = !empty($product['IMAGE']) ? $product['IMAGE'] : '/images/product_sample.jpg'; 
            $qty = 1;

            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] += $qty;
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'image' => $image,
                    'qty' => $qty
                ];
            }
        }
    } catch (PDOException $e) {} 
    $referer = $_SERVER['HTTP_REFERER'] ?? '/shop.php';
    header("Location: $referer");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $image = $_POST['image'];
    $qty = (int)$_POST['qty'];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'qty' => $qty
        ];
    }
    
    header("Location: /cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            if (isset($_SESSION['cart'][$id])) {
                $qty = (int)$qty;
                if ($qty > 0) {
                    $_SESSION['cart'][$id]['qty'] = $qty;
                } else {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }
    }
    header("Location: /cart.php");
    exit;
}

if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
    }
    header("Location: /cart.php");
    exit;
}

$cartItems = $_SESSION['cart'] ?? [];
$totalPrice = 0;
$totalQuantity = 0;

foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['qty'];
    $totalQuantity += $item['qty'];
}

include '../views/components/header.php';
include '../views/components/navbar.php';
include '../views/cart.php';
include '../views/components/footer.php';
?>