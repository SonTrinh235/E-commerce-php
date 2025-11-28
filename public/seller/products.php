<?php
session_start();
require_once '../../config/database.php'; 
include "../../views/components/header.php";
include "../../views/components/navbar.php";

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    header("Location: /login.php");
    exit;
}

$seller = $_SESSION['logged_in_user'];
$sellerId = $seller['id'];
$message = "";

if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = $_POST['product_id'];
    
    try {
        $sql = "CALL DELETE_PRODUCTS(:id)"; 
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_to_delete);
        $stmt->execute();
        
        $message = "<div class='alert alert-success'>Đã xóa sản phẩm thành công!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
}

$sellerProducts = [];
$keyword = $_GET['keyword'] ?? '';

try {
    $sql = "SELECT * FROM PRODUCTS WHERE SELLERID = :sid";
    
    if ($keyword) {
        $sql .= " AND PRO_NAME LIKE :kw";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sid', $sellerId);
    if ($keyword) {
        $kw_param = "%$keyword%";
        $stmt->bindParam(':kw', $kw_param);
    }
    $stmt->execute();
    $sellerProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
}
?>

<div class="container mt-5">
    <h2>Quản lý sản phẩm (<?= htmlspecialchars($sellerId) ?>)</h2>
    <?= $message ?>
    
    <div class="d-flex justify-content-between mb-3">
        <form method="GET" class="d-flex">
            <input type="text" name="keyword" class="form-control me-2" placeholder="Tìm tên sản phẩm..." value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-primary">Tìm</button>
        </form>
        <a href="add-product.php" class="btn btn-success">Thêm mới</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã SP</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sellerProducts as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['PRODUCTID']) ?></td>
                <td><?= htmlspecialchars($p['PRO_NAME']) ?></td>
                <td><?= htmlspecialchars($p['CAT_NAME']) ?></td>
                <td><?= number_format($p['PRO_PRICE']) ?> VNĐ</td>
                <td>
                    <form method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= $p['PRODUCTID'] ?>">
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include "../../views/components/footer.php"; ?>