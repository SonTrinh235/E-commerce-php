<?php
session_start();
require_once '../../config/database.php';
include "../../views/components/header.php";
include "../../views/components/navbar.php";

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    echo "<script>window.location.href='/login.php';</script>";
    exit;
}

$sellerId = $_SESSION['logged_in_user']['id'];
$orders = [];
$error = null;

try {
    $sql = "SELECT o.ORDERID, o.SHIP_DATE, o.TOTAL_PRICE,
                   u.FIRSTNAME, u.LASTNAME, u.PHONE,
                   c.CONF_STATUS
            FROM ORDERS o
            JOIN CONFIRMS c ON o.ORDERID = c.ORDERID
            JOIN USERS u ON o.BUYERID = u.USERID
            WHERE c.SELLERID = :sid
            GROUP BY o.ORDERID, o.SHIP_DATE, o.TOTAL_PRICE, u.FIRSTNAME, u.LASTNAME, u.PHONE, c.CONF_STATUS
            ORDER BY o.SHIP_DATE DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':sid' => $sellerId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Lỗi tải đơn hàng: " . $e->getMessage();
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">📦 Quản Lý Đơn Hàng</h2>
        <span class="badge bg-secondary fs-6"><?= count($orders) ?> đơn hàng</span>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Ngày Giao (Dự kiến)</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th class="text-end pe-4">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#<?= htmlspecialchars($order['ORDERID']) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($order['FIRSTNAME'] . ' ' . $order['LASTNAME']) ?></div>
                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($order['PHONE']) ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($order['SHIP_DATE'])) ?></td>
                                    <td class="fw-bold text-danger"><?= number_format($order['TOTAL_PRICE']) ?> ₫</td>
                                    <td>
                                        <?php 
                                            $statusClass = 'secondary';
                                            if ($order['CONF_STATUS'] === 'Đã xác nhận') $statusClass = 'success';
                                            elseif ($order['CONF_STATUS'] === 'Chưa xác nhận') $statusClass = 'warning text-dark';
                                            elseif ($order['CONF_STATUS'] === 'Đã hủy') $statusClass = 'danger';
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?> bg-opacity-25 px-3 py-2 rounded-pill border border-<?= $statusClass ?>">
                                            <?= htmlspecialchars($order['CONF_STATUS']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="view-order.php?id=<?= $order['ORDERID'] ?>" class="btn btn-sm btn-outline-primary shadow-sm">
                                            Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="mb-0">Chưa có đơn hàng nào cần xử lý.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../../views/components/footer.php"; ?>