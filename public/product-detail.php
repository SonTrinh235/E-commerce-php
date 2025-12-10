<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php'; 

$productId = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;
$reviews = [];
$hasReviewed = false; 
$user = $_SESSION['logged_in_user'] ?? null;

if ($productId) {
    try {
        $sql = 'SELECT p.*, 
                       c.CAT_NAME, 
                       s.SHOP_NAME, 
                       s.SELLERID,
                       u.PRO_QUANTITY, 
                       (SELECT COALESCE(AVG(r.REV_RATING), 0) 
                        FROM REVIEWS r 
                        WHERE r.PRODUCTID = p.PRODUCTID) as AVG_RATING
                FROM PRODUCTS p
                LEFT JOIN CATEGORIES c ON p.CAT_NAME = c.CAT_NAME
                LEFT JOIN SELLERS s ON p.SELLERID = s.SELLERID
                LEFT JOIN UPDATES u ON p.PRODUCTID = u.PRODUCTID AND p.SELLERID = u.SELLERID
                WHERE p.PRODUCTID = :pid';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pid', $productId);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            if ($user && isset($user['role']) && $user['role'] === 'buyer') {
                $stmtCheck = $conn->prepare("SELECT 1 FROM REVIEWS WHERE BUYERID = :bid AND PRODUCTID = :pid");
                $stmtCheck->execute([':bid' => $user['id'], ':pid' => $productId]);
                if ($stmtCheck->fetch()) {
                    $hasReviewed = true;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
                if ($user && isset($user['role']) && $user['role'] === 'buyer' && !$hasReviewed) {
                    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
                    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
                    
                    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
                        $sqlInsert = "INSERT INTO REVIEWS (BUYERID, PRODUCTID, REV_TEXT, REV_RATING) 
                                      VALUES (:bid, :pid, :txt, :rate)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->execute([
                            ':bid' => $user['id'],
                            ':pid' => $productId,
                            ':txt' => $comment,
                            ':rate' => $rating
                        ]);
                        
                        header("Location: /product-detail.php?id=$productId");
                        exit;
                    }
                }
            }

            $sqlReviews = "SELECT r.*, u.FIRSTNAME, u.LASTNAME 
                           FROM REVIEWS r
                           JOIN USERS u ON r.BUYERID = u.USERID
                           WHERE r.PRODUCTID = :pid
                           ORDER BY r.REV_RATING DESC"; 
            $stmtRv = $conn->prepare($sqlReviews);
            $stmtRv->execute([':pid' => $productId]);
            $reviews = $stmtRv->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        error_log($e->getMessage());
        die("Hệ thống đang bảo trì. Vui lòng quay lại sau.");
    }
}

include "../views/components/header.php";
include "../views/components/navbar.php";
include "../views/product-detail.php";
include "../views/components/footer.php";
?>