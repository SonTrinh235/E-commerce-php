<?php
session_start();
require_once '../../config/database.php';
include "../../views/components/header.php";
include "../../views/components/navbar.php";

// 1. Kiểm tra quyền Seller
if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    echo "<script>window.location.href='/login.php';</script>";
    exit;
}

$sellerId = $_SESSION['logged_in_user']['id'];
$orderId = $_GET['id'] ?? '';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $newStatus = $_POST['status'];
    
    try {
        $sqlUpdate = "UPDATE CONFIRMS 
                      SET CONF_STATUS = :status 
                      WHERE ORDERID = :oid AND SELLERID = :sid";
        
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->execute([
            ':status' => $newStatus,
            ':oid' => $orderId,
            ':sid' => $sellerId
        ]);
        
        $message = "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Cập nhật thành công!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
}

$orderInfo = null;
$orderItems = [];

try {
    // A. Lấy thông tin khách hàng từ bảng ORDERS và USERS
    $sqlInfo = "SELECT o.ORDERID, o.SHIP_DATE, o.SHIP_TIME, u.FIRSTNAME, u.LASTNAME, u.PHONE, u.ADDRESS
                FROM ORDERS o
                JOIN USERS u ON o.BUYERID = u.USERID
                WHERE o.ORDERID = :oid";
    $stmt = $conn->prepare($sqlInfo);
    $stmt->execute([':oid' => $orderId]);
    $orderInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    // B. Lấy danh sách sản phẩm CỦA SHOP NÀY trong đơn hàng
    $sqlItems = "SELECT p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.IMAGE, 
                        c.CONF_STATUS
                 FROM CONFIRMS c
                 JOIN PRODUCTS p ON c.PRODUCTID = p.PRODUCTID
                 WHERE c.ORDERID = :oid AND c.SELLERID = :sid";
    $stmtItems = $conn->prepare($sqlItems);
    $stmtItems->execute([':oid' => $orderId, ':sid' => $sellerId]);
    $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="orders.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
        <h3 class="text-primary fw-bold">Chi Tiết Đơn Hàng #<?= htmlspecialchars($orderId) ?></h3>
    </div>
    
    <?= $message ?>

    <?php if ($orderInfo): ?>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light fw-bold">Thông tin khách hàng</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($orderInfo['FIRSTNAME'] . ' ' . $orderInfo['LASTNAME']) ?></h5>
                        <p class="mb-1"><i class="bi bi-telephone me-2"></i> <?= htmlspecialchars($orderInfo['PHONE']) ?></p>
                        <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($orderInfo['ADDRESS']) ?></p>
                        <hr>
                        <p class="mb-1 text-muted small">Ngày giao dự kiến: <?= date('d/m/Y', strtotime($orderInfo['SHIP_DATE'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                        <span>Sản phẩm cần giao</span>
                        <span class="badge bg-warning text-dark">
                            <?= htmlspecialchars($orderItems[0]['CONF_STATUS'] ?? 'Chưa xác nhận') ?>
                        </span>
                    </div>
                    
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Sản phẩm</th>
                                    <th class="text-end pe-3">Giá bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($item['IMAGE'] ?? '/images/product_sample.jpg') ?>" 
                                                     width="50" height="50" class="rounded border me-2" style="object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($item['PRO_NAME']) ?></div>
                                                    <small class="text-muted">ID: <?= htmlspecialchars($item['PRODUCTID']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-3 fw-bold"><?= number_format($item['PRO_PRICE']) ?> ₫</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer bg-light p-3">
                        <form method="post" class="row align-items-center g-2">
                            <input type="hidden" name="action" value="update_status">
                            <div class="col-auto fw-bold">Cập nhật trạng thái:</div>
                            <div class="col-auto flex-grow-1">
                                <select name="status" class="form-select border-primary">
                                    <option value="Chưa xác nhận">Chưa xác nhận</option>
                                    <option value="Đã xác nhận" selected>Đã xác nhận</option>
                                    <option value="Đang giao">Đang giao</option>
                                    <option value="Đã giao">Đã giao</option>
                                    <option value="Đã hủy">Hủy đơn</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-save me-1"></i> Lưu
                                </button>
                            </div>
                        </form>
                        <div class="form-text mt-2 text-muted small">
                            <i class="bi bi-info-circle"></i> Chọn <strong>"Đã xác nhận"</strong> để đơn hàng được tính vào doanh thu.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Không tìm thấy thông tin đơn hàng hoặc bạn không có quyền xem đơn này.</div>
    <?php endif; ?>
</div>

<?php include "../../views/components/footer.php"; ?>