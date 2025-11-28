<?php
session_start();
require_once '../config/database.php'; 

$products = [];
$categories = [];

$selectedCategory = $_GET['category'] ?? 'All';
$maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 9999999999; 
$keyword = trim($_GET['keyword'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

try {
    $catStmt = $conn->query("SELECT CAT_NAME FROM CATEGORIES");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    $sql = "SELECT p.*, s.SHOP_NAME 
            FROM PRODUCTS p 
            LEFT JOIN SELLERS s ON p.SELLERID = s.SELLERID 
            WHERE 1=1";
    
    $params = [];

    if ($selectedCategory !== 'All' && !empty($selectedCategory)) {
        $sql .= " AND (p.CAT_NAME = :cat OR p.CAT_NAME IN (SELECT sub.CAT_NAME FROM CATEGORIES sub WHERE sub.PARENTCAT_NAME = :cat))";
        $params[':cat'] = $selectedCategory;
    }
    $sql .= " AND p.PRO_PRICE <= :price";
    $params[':price'] = $maxPrice;

    if (!empty($keyword)) {
        $sql .= " AND p.PRO_NAME LIKE :kw";
        $params[':kw'] = "%$keyword%";
    }

    if ($sort === 'price_asc') {
        $sql .= " ORDER BY p.PRO_PRICE ASC";
    } elseif ($sort === 'price_desc') {
        $sql .= " ORDER BY p.PRO_PRICE DESC";
    } else {
        $sql .= " ORDER BY p.PRODUCTID DESC"; 
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}

include '../views/components/header.php';
include '../views/components/navbar.php';
include '../views/shop.php';
include '../views/components/footer.php';
?>