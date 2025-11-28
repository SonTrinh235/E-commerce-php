<?php
$host = 'localhost';
$user = 'root';
$password = 'Cuccutthan1@'; 
$dbname = 'ecommercephp';

$products = [];

try {
    $conn = mysqli_connect($host, $user, $password, $dbname);

    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }

    $sql = 'SELECT 
                p.*, 
                c.CAT_NAME AS CategoryName, 
                s.SELLERID 
            FROM 
                PRODUCTS p
            JOIN 
                CATEGORIES c ON p.CAT_NAME = c.CAT_NAME  
            JOIN 
                SELLERS s ON p.SELLERID = s.SELLERID
            LIMIT 8';
            
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    mysqli_free_result($result);
    mysqli_close($conn);

} catch (Exception $e) {
    error_log($e->getMessage()); 
    $products = []; 
}
?>

<?php 
include "components/header.php"; 
include "components/navbar.php"; 
?>

<div class="container mt-5">
    
    <div class="p-5 text-center bg-light rounded-3 mb-5">
        <h1 class="text-dark">Chào mừng đến với E-commerce PHP</h1>
        <p class="lead">Khám phá những sản phẩm mới nhất và tốt nhất của chúng tôi!</p>
        <a href="shop.php" class="btn btn-primary btn-lg mt-3">Mua sắm ngay</a>
    </div>

    <h3 class="mb-4 section-title text-center">Sản Phẩm Nổi Bật</h3>
    <div class="row g-4">
        <?php
        if (!empty($products)) {
            foreach ($products as $product) {
                include "components/product-card.php";
            }
        } else {
            echo "<p class='col-12 text-center'>Không có sản phẩm nào để hiển thị.</p>";
        }
        ?>
    </div>
    
    <div class="text-center mt-5">
        <a href="shop.php" class="btn btn-outline-dark btn-lg">Xem toàn bộ cửa hàng</a>
    </div>

</div>

<?php 
include "components/footer.php"; 
?>