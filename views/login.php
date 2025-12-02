<?php
session_start();

// 1. Kết nối Database
// File này nằm ở public/login.php, nên dùng ../ để ra thư mục gốc lấy config
require_once '../config/database.php'; 
include "../views/components/header.php";
include "../views/components/navbar.php";

$errors = [];

// 2. Xử lý khi người dùng ấn nút Đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $errors[] = "Vui lòng nhập tên đăng nhập và mật khẩu.";
    } else {
        try {
            // A. Tìm user trong bảng USERS (So khớp username & password)
            // Lưu ý: DB của bạn đang lưu pass dạng text thường (abc3), nên so sánh trực tiếp
            $sql = "SELECT * FROM users WHERE USERNAME = :username AND USER_PASSWORD = :password";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // B. User tồn tại -> Xác định Role (Seller hay Buyer?)
                $userId = $user['USERID'];
                $role = 'unknown'; 
                $extraInfo = [];

                // Kiểm tra bảng SELLERS
                $stmtSeller = $conn->prepare("SELECT * FROM sellers WHERE SELLERID = :id");
                $stmtSeller->bindParam(':id', $userId);
                $stmtSeller->execute();
                $sellerData = $stmtSeller->fetch(PDO::FETCH_ASSOC);

                if ($sellerData) {
                    $role = 'seller';
                    $extraInfo = $sellerData; // Lưu tên Shop, Rating...
                } else {
                    // Nếu không phải Seller, kiểm tra bảng BUYERS
                    $stmtBuyer = $conn->prepare("SELECT * FROM buyers WHERE BUYERID = :id");
                    $stmtBuyer->bindParam(':id', $userId);
                    $stmtBuyer->execute();
                    $buyerData = $stmtBuyer->fetch(PDO::FETCH_ASSOC);

                    if ($buyerData) {
                        $role = 'buyer';
                        $extraInfo = $buyerData; // Lưu Membership Level...
                    }
                }

                // C. Lưu Session
                $_SESSION['logged_in_user'] = [
                    'id' => $user['USERID'],
                    'username' => $user['USERNAME'],
                    'fullname' => $user['FIRSTNAME'] . ' ' . $user['LASTNAME'],
                    'role' => $role,     // Quan trọng: dùng để check quyền ở các trang khác
                    'info' => $extraInfo 
                ];

                // D. Chuyển hướng theo Role
                if ($role === 'seller') {
                    header("Location: /seller/index.php"); // Vào Dashboard người bán
                    exit;
                } elseif ($role === 'buyer') {
                    header("Location: /index.php");        // Vào trang chủ người mua
                    exit;
                } else {
                    // Trường hợp user có trong bảng USERS nhưng không có trong SELLERS lẫn BUYERS
                    $errors[] = "Tài khoản chưa được kích hoạt vai trò người bán hoặc người mua.";
                    session_unset();
                    session_destroy();
                }

            } else {
                $errors[] = "Tên đăng nhập hoặc mật khẩu không đúng.";
            }
        } catch (PDOException $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5" style="max-width: 450px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h2 class="text-center mb-4">Đăng Nhập</h2>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach($errors as $err) echo "<li>$err</li>"; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input class="form-control" type="text" name="username" placeholder="" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input class="form-control" type="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
            </form>

            <p class="text-center mt-3 mb-0">
                Chưa có tài khoản? <a href="/register.php">Đăng ký ngay</a>
            </p>
        </div>
    </div>
    
    <div class="alert alert-info mt-4">
        <strong>Test:</strong><br>
        Seller: <code>ABC3</code> / <code>abc3</code><br>
        Buyer: <code>ABC1</code> / <code>abc1</code>
    </div>
</div>

<?php include "../views/components/footer.php"; ?>