<?php
session_start();
require_once '../config/database.php'; 

$productId = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;

if ($productId) {
    try {
        $sql = 'SELECT p.*, c.CAT_NAME, s.SHOP_NAME, s.SELLERID
                FROM PRODUCTS p
                LEFT JOIN CATEGORIES c ON p.CAT_NAME = c.CAT_NAME
                LEFT JOIN SELLERS s ON p.SELLERID = s.SELLERID
                WHERE p.PRODUCTID = :pid';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pid', $productId);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}

include "../views/components/header.php";
include "../views/components/navbar.php";
include "../views/product-detail.php";
include "../views/components/footer.php";
?>