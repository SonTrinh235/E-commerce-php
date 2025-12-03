<?php
session_start();
require_once '../config/database.php';
include "../views/components/header.php";
include "../views/components/navbar.php";

$user = $_SESSION['logged_in_user'] ?? null;
if (!$user || $user['role'] !== 'buyer') {
    echo "<script>alert('Vui lòng đăng nhập tài khoản người mua!'); window.location.href='/login.php';</script>";
    exit;
}

$buyerId = $user['id'];
$message = "";

$cartItems = [];
$totalPrice = 0;
$totalQty = 0;
$cartId = '';

try {
    $stmtCart = $conn->prepare("SELECT CARTID FROM CARTS WHERE BUYERID = :bid");
    $stmtCart->execute([':bid' => $buyerId]);
    $cartRow = $stmtCart->fetch(PDO::FETCH_ASSOC);
    
    if ($cartRow) {
        $cartId = $cartRow['CARTID'];
        
        $sqlItems = "SELECT p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.SELLERID, 
                            COUNT(s.PRODUCTID) as QUANTITY
                     FROM STORES s
                     JOIN PRODUCTS p ON s.PRODUCTID = p.PRODUCTID
                     WHERE s.CARTID = :cid
                     GROUP BY p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.SELLERID";
                     
        $stmtItems = $conn->prepare($sqlItems);
        $stmtItems->execute([':cid' => $cartId]);
        $cartItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cartItems as $item) {
            $totalPrice += $item['PRO_PRICE'] * $item['QUANTITY'];
            $totalQty += $item['QUANTITY'];
        }
    }
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Lỗi tải giỏ hàng: " . $e->getMessage() . "</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (empty($cartItems)) {
        $message = "<div class='alert alert-warning'>Giỏ hàng trống!</div>";
    } else {
        $paymentMethod = $_POST['payment_method'] ?? 'Tiền mặt';
        if (!in_array($paymentMethod, ['Tiền mặt', 'Chuyển khoản'])) {
            $paymentMethod = 'Tiền mặt';
        }

        try {
            $conn->beginTransaction();

            $createdOrderIds = []; 
            $now = time();
            $shipDate = date('Y-m-d', strtotime('+3 days'));
            $shipTime = date('H:i:s');

            foreach ($cartItems as $index => $item) {
                $timePart = $now % 100000;
                $randPart = rand(10, 99);
                $thisOrderId = 'O' . $timePart . $index . $randPart;
                $thisOrderId = substr($thisOrderId, 0, 10);
                $createdOrderIds[] = $thisOrderId; 

                $thisOrderTotal = $item['PRO_PRICE'] * $item['QUANTITY'];

                $sqlOrder = "INSERT INTO ORDERS (ORDERID, BUYERID, ORD_QUANTITY, TOTAL_PRICE, TOTAL_DISCOUNT_PRICE, SHIP_TIME, SHIP_DATE) 
                             VALUES (:oid, :bid, :qty, :total, :discount, :stime, :sdate)";
                $stmtOrder = $conn->prepare($sqlOrder);
                $stmtOrder->execute([
                    ':oid' => $thisOrderId,
                    ':bid' => $buyerId,
                    ':qty' => $item['QUANTITY'], 
                    ':total' => $thisOrderTotal, 
                    ':discount' => $thisOrderTotal,
                    ':stime' => $shipTime,
                    ':sdate' => $shipDate
                ]);

                $sqlConfirm = "INSERT INTO CONFIRMS (PRODUCTID, ORDERID, SELLERID, CONF_TIME, CONF_DATE, CONF_STATUS) 
                               VALUES (:pid, :oid, :sid, CURTIME(), CURDATE(), 'Chưa xác nhận')";
                $stmtConfirm = $conn->prepare($sqlConfirm);
                $stmtConfirm->execute([
                    ':pid' => $item['PRODUCTID'],
                    ':oid' => $thisOrderId,
                    ':sid' => $item['SELLERID']
                ]);

                $stmtPlace = $conn->prepare("INSERT INTO PLACES (BUYERID, PRODUCTID) VALUES (:bid, :pid)");
                $stmtPlace->execute([':bid' => $buyerId, ':pid' => $item['PRODUCTID']]);
                
                $payNumber = 'P' . $timePart . $index . $randPart;
                $payNumber = substr($payNumber, 0, 10);
                $payStatus = ($paymentMethod === 'Chuyển khoản') ? 'Đã thanh toán' : 'Chưa thanh toán';

                $sqlPay = "INSERT INTO PAYMENTS (ORDERID, PAY_NUMBER, PAY_TIME, PAY_DATE, PAY_METHOD, STATUS_OF_ORDER)
                           VALUES (:oid, :pnum, CURTIME(), CURDATE(), :method, :status)";
                $stmtPay = $conn->prepare($sqlPay);
                $stmtPay->execute([
                    ':oid' => $thisOrderId,
                    ':pnum' => $payNumber,
                    ':method' => $paymentMethod,
                    ':status' => $payStatus
                ]);
            }

            $stmtClear = $conn->prepare("DELETE FROM STORES WHERE CARTID = :cid");
            $stmtClear->execute([':cid' => $cartId]);

            $conn->commit();
            
            $orderListString = implode(', ', $createdOrderIds);
            echo "<div class='container mt-5'>
                    <div class='alert alert-success text-center py-5'>
                        <h1 class='display-4'><i class='bi bi-check-circle text-success'></i></h1>
                        <h3>Đặt hàng thành công!</h3>
                        <p class='mb-1'>Phương thức thanh toán: <strong>$paymentMethod</strong></p>
                        <p class='small text-muted'>Mã đơn: <strong>$orderListString</strong></p>
                        <a href='/my-orders.php' class='btn btn-primary mt-3'>Xem lịch sử đơn hàng</a>
                    </div>
                  </div>";
            include "../views/components/footer.php";
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "<div class='alert alert-danger'>Lỗi đặt hàng: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="container mt-5" style="max-width: 800px;">
    <h2 class="mb-4 fw-bold text-center">Xác Nhận Đơn Hàng</h2>
    
    <?= $message ?>

    <?php if (!empty($cartItems)): ?>
        
        <form method="post">
            <input type="hidden" name="action" value="checkout">

            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Lưu ý: Mỗi sản phẩm sẽ được tách thành một đơn hàng riêng biệt.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Chi tiết đơn hàng</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($cartItems as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($item['PRO_NAME']) ?></strong>
                                    <br><small class="text-muted">Đơn giá: <?= number_format($item['PRO_PRICE']) ?> ₫</small>
                                </div>
                                <div class="text-end">
                                    <span>x <?= $item['QUANTITY'] ?></span><br>
                                    <strong class="text-primary"><?= number_format($item['PRO_PRICE'] * $item['QUANTITY']) ?> ₫</strong>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between fs-5 fw-bold border-top pt-3">
                        <span>Tổng tiền phải trả:</span>
                        <span class="text-danger"><?= number_format($totalPrice) ?> ₫</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Phương thức thanh toán</div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input payment-radio" type="radio" name="payment_method" id="pay_cash" value="Tiền mặt" checked>
                        <label class="form-check-label fw-bold" for="pay_cash">
                            <i class="bi bi-cash-coin me-2 text-success"></i> Thanh toán khi nhận hàng (COD)
                        </label>
                        <div class="text-muted small ms-4">Thanh toán bằng tiền mặt khi giao hàng.</div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input payment-radio" type="radio" name="payment_method" id="pay_bank" value="Chuyển khoản">
                        <label class="form-check-label fw-bold" for="pay_bank">
                            <i class="bi bi-bank me-2 text-primary"></i> Chuyển khoản ngân hàng (QR Code)
                        </label>
                        <div class="text-muted small ms-4">Quét mã QR để thanh toán ngay lập tức.</div>
                    </div>

                    <div id="qr_container" class="mt-4 text-center border p-3 rounded bg-light" style="display: none;">
                        <h6 class="fw-bold text-primary mb-3">Quét mã để thanh toán</h6>
                        <img src="/images/qr.jpg" alt="QR Code Bank" style="width: 250px; border: 1px solid #ddd; border-radius: 8px;">
                        
                        <div class="mt-3 small">
                            <p class="mb-1">Ngân hàng: <strong>MB Bank</strong></p>
                            <p class="mb-1">STK: <strong>0334759902</strong></p>
                            <p class="mb-1">Chủ TK: <strong>TRINH DUC SON</strong></p>
                            <p class="text-danger fw-bold">Nội dung CK: Tên bạn + SĐT</p>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm">
                XÁC NHẬN THANH TOÁN (<?= count($cartItems) ?> ĐƠN)
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="/cart.php" class="text-decoration-none text-secondary">Quay lại giỏ hàng</a>
        </div>

    <?php else: ?>
        <div class="alert alert-warning text-center">
            Giỏ hàng trống. <a href="/shop.php">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById('qr_container');
        const radios = document.querySelectorAll('.payment-radio');

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'Chuyển khoản') {
                    qrContainer.style.display = 'block';
                } else {
                    qrContainer.style.display = 'none';
                }
            });
        });
    });
</script>

<?php include "../views/components/footer.php"; ?>