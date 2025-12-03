<?php
session_start();
require_once '../config/database.php'; 

$productId = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;
$reviews = [];
$hasReviewed = false; 
$user = $_SESSION['logged_in_user'] ?? null;

if ($productId) {
    try {
        // --- SỬA LẠI: Dùng Sub-query tính điểm trung bình (An toàn hơn gọi Function) ---
        // COALESCE(..., 0) để nếu chưa có đánh giá thì trả về 0 luôn thay vì null
        $sql = 'SELECT p.*, c.CAT_NAME, s.SHOP_NAME, s.SELLERID,
                (SELECT COALESCE(AVG(r.REV_RATING), 0) 
                 FROM REVIEWS r 
                 WHERE r.PRODUCTID = p.PRODUCTID) as AVG_RATING
                FROM PRODUCTS p
                LEFT JOIN CATEGORIES c ON p.CAT_NAME = c.CAT_NAME
                LEFT JOIN SELLERS s ON p.SELLERID = s.SELLERID
                WHERE p.PRODUCTID = :pid';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pid', $productId);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // --- DEBUG: Nếu vẫn lỗi thì mở đoạn này ra để xem ---
        // if (!$product && $stmt->errorCode() !== '00000') {
        //     print_r($stmt->errorInfo()); die();
        // }

        // Logic kiểm tra xem user đã review chưa
        if ($user && isset($user['role']) && $user['role'] === 'buyer') {
            $stmtCheck = $conn->prepare("SELECT 1 FROM REVIEWS WHERE BUYERID = :bid AND PRODUCTID = :pid");
            $stmtCheck->execute([':bid' => $user['id'], ':pid' => $productId]);
            if ($stmtCheck->fetch()) {
                $hasReviewed = true;
            }
        }

        // Logic thêm review mới
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
            if ($user && isset($user['role']) && $user['role'] === 'buyer' && !$hasReviewed) {
                $rating = (int)$_POST['rating'];
                $comment = trim($_POST['comment']);
                
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

        // Lấy danh sách review để hiển thị bên dưới
        $sqlReviews = "SELECT r.*, u.FIRSTNAME, u.LASTNAME 
                       FROM REVIEWS r
                       JOIN USERS u ON r.BUYERID = u.USERID
                       WHERE r.PRODUCTID = :pid
                       ORDER BY r.REV_RATING DESC"; 
        $stmtRv = $conn->prepare($sqlReviews);
        $stmtRv->execute([':pid' => $productId]);
        $reviews = $stmtRv->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Hiển thị lỗi ra màn hình nếu có lỗi kết nối để dễ debug
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}

include "../views/components/header.php";
include "../views/components/navbar.php";
include "../views/product-detail.php";
include "../views/components/footer.php";
?>