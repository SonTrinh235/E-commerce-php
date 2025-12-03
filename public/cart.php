<?php
session_start();
require_once '../config/database.php'; 

$user = $_SESSION['logged_in_user'] ?? null;
if (!$user || $user['role'] !== 'buyer') {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
        exit;
    }
    header("Location: /login.php");
    exit;
}

$buyerId = $user['id']; 

function getCurrentCartId($conn, $buyerId) {
    $stmt = $conn->prepare("SELECT CARTID FROM CARTS WHERE BUYERID = :bid LIMIT 1");
    $stmt->execute([':bid' => $buyerId]);
    return $stmt->fetchColumn(); 
}

function calculateCartTotal($conn, $cartId) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM STORES WHERE CARTID = :cid");
    $stmt->execute([':cid' => $cartId]);
    return (int)$stmt->fetchColumn();
}

$cartId = getCurrentCartId($conn, $buyerId);
if ((isset($_POST['action']) && $_POST['action'] === 'add') || (isset($_GET['action']) && $_GET['action'] === 'add')) {
    $productId = $_POST['id'] ?? $_GET['id'] ?? '';
    $qty = (int)($_POST['qty'] ?? 1);

    if ($productId) {
        try {
            $conn->beginTransaction();

            if (!$cartId) {
                $cartId = 'CART_' . time() . rand(100, 999);
                if(strlen($cartId) > 10) $cartId = substr($cartId, 0, 10);
                
                $sqlCart = "INSERT INTO CARTS (CARTID, CART_TIME, CART_DATE, CART_QUANTITY, BUYERID) 
                            VALUES (:cid, CURTIME(), CURDATE(), :qty, :bid)";
                $conn->prepare($sqlCart)->execute([':cid' => $cartId, ':qty' => $qty, ':bid' => $buyerId]);
            }

            $sqlStore = "INSERT INTO STORES (CARTID, PRODUCTID) VALUES (:cid, :pid)";
            $stmtStore = $conn->prepare($sqlStore);
            for ($i = 0; $i < $qty; $i++) {
                $stmtStore->execute([':cid' => $cartId, ':pid' => $productId]);
            }

            if ($cartId) {
                $newTotal = calculateCartTotal($conn, $cartId);
                $conn->prepare("UPDATE CARTS SET CART_QUANTITY = ? WHERE CARTID = ?")->execute([$newTotal, $cartId]);
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            die("Lỗi thêm giỏ hàng: " . $e->getMessage());
        }
    }
    
    $referer = $_SERVER['HTTP_REFERER'] ?? '/shop.php';
    if (strpos($referer, 'cart.php') !== false) header("Location: /cart.php");
    else header("Location: $referer");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        try {
            $conn->beginTransaction();
            foreach ($_POST['qty'] as $pid => $q) {
                $q = (int)$q;
                
                $conn->prepare("DELETE FROM STORES WHERE CARTID = :cid AND PRODUCTID = :pid")
                     ->execute([':cid' => $cartId, ':pid' => $pid]);

                if ($q > 0) {
                    $stmtIns = $conn->prepare("INSERT INTO STORES (CARTID, PRODUCTID) VALUES (:cid, :pid)");
                    for ($i = 0; $i < $q; $i++) {
                        $stmtIns->execute([':cid' => $cartId, ':pid' => $pid]);
                    }
                }
            }

            $newTotal = calculateCartTotal($conn, $cartId);
            if ($newTotal > 0) {
                $conn->prepare("UPDATE CARTS SET CART_QUANTITY = ? WHERE CARTID = ?")->execute([$newTotal, $cartId]);
            } else {
                $conn->prepare("DELETE FROM CARTS WHERE CARTID = ?")->execute([$cartId]);
            }

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            die("Lỗi update: " . $e->getMessage());
        }
    }
    header("Location: /cart.php");
    exit;
}

if (isset($_GET['remove'])) {
    $pid = $_GET['remove'];
    try {
        $conn->beginTransaction();
        
        $conn->prepare("DELETE FROM STORES WHERE CARTID = :cid AND PRODUCTID = :pid")
             ->execute([':cid' => $cartId, ':pid' => $pid]);

        $newTotal = calculateCartTotal($conn, $cartId);
        if ($newTotal > 0) {
            $conn->prepare("UPDATE CARTS SET CART_QUANTITY = ? WHERE CARTID = ?")->execute([$newTotal, $cartId]);
        } else {
            $conn->prepare("DELETE FROM CARTS WHERE CARTID = ?")->execute([$cartId]);
        }

        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Lỗi xóa: " . $e->getMessage());
    }
    header("Location: /cart.php");
    exit;
}

$cartItems = [];
$totalPrice = 0;

if ($cartId) { 
    try {
        $sql = "SELECT p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.IMAGE, 
                       COUNT(*) as QUANTITY
                FROM STORES s
                JOIN PRODUCTS p ON s.PRODUCTID = p.PRODUCTID
                WHERE s.CARTID = :cid
                GROUP BY p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.IMAGE";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':cid' => $cartId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $cartItems[] = [
                'id' => $row['PRODUCTID'],
                'name' => $row['PRO_NAME'],
                'price' => (float)$row['PRO_PRICE'],
                'image' => $row['IMAGE'],
                'qty' => (int)$row['QUANTITY']
            ];
            $totalPrice += (float)$row['PRO_PRICE'] * (int)$row['QUANTITY'];
        }
    } catch (PDOException $e) {
        echo "Lỗi hiển thị: " . $e->getMessage();
    }
}

include '../views/components/header.php';
include '../views/components/navbar.php';
include '../views/cart.php';
include '../views/components/footer.php';
?>