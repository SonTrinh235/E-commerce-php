<?php
session_start();
require_once '../config/database.php';
include "../views/components/header.php";
include "../views/components/navbar.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $ssn       = trim($_POST['ssn'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $role      = $_POST['role'] ?? 'buyer'; 

    if (!preg_match('/^[A-Za-z0-9]+$/', $username)) {
        $errors[] = "Username chỉ được chứa chữ và số.";
    }
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*[0-9]).{5,}$/', $password)) {
        $errors[] = "Mật khẩu phải có ít nhất 1 chữ, 1 số và dài tối thiểu 5 ký tự.";
    }
    if (!preg_match('/^0[0-9]{9}$/', $phone)) {
        $errors[] = "Số điện thoại phải bắt đầu bằng số 0 và có 10 chữ số.";
    }

    $stmt = $conn->prepare("SELECT 1 FROM users WHERE USERNAME = :u");
    $stmt->execute([':u' => $username]);
    if ($stmt->fetch()) {
        $errors[] = "Tên đăng nhập đã tồn tại.";
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            $prefix = ($role === 'seller') ? 'S_' : 'B_';
            $newId = $prefix . rand(1000, 99999); 
            
            while(true) {
                $check = $conn->prepare("SELECT 1 FROM users WHERE USERID = ?");
                $check->execute([$newId]);
                if(!$check->fetch()) break;
                $newId = $prefix . rand(1000, 99999);
            }

            $sqlUser = "INSERT INTO USERS (USERID, SSN, USERNAME, USER_PASSWORD, FIRSTNAME, LASTNAME, ADDRESS, PHONE) 
                        VALUES (:id, :ssn, :user, :pass, :fname, :lname, :addr, :phone)";
            $stmtUser = $conn->prepare($sqlUser);
            $stmtUser->execute([
                ':id' => $newId,
                ':ssn' => $ssn,
                ':user' => $username,
                ':pass' => $password,
                ':fname' => $firstname,
                ':lname' => $lastname,
                ':addr' => $address,
                ':phone' => $phone
            ]);

            $sqlEmail = "INSERT INTO USER_EMAILS (USERID, EMAIL) VALUES (:id, :email)";
            $stmtEmail = $conn->prepare($sqlEmail);
            $stmtEmail->execute([':id' => $newId, ':email' => $email]);
            if ($role === 'seller') {
                $shopName = "Shop của " . $lastname;
                $sqlSeller = "INSERT INTO SELLERS (SELLERID, SHOP_NAME, SHOP_RATING) VALUES (:id, :shop, 0)";
                $stmtRole = $conn->prepare($sqlSeller);
                $stmtRole->execute([':id' => $newId, ':shop' => $shopName]);
            } else {
                // Nếu là Buyer
                $sqlBuyer = "INSERT INTO BUYERS (BUYERID, MEMBERSHIP_LEVEL, REWARD_POINTS) VALUES (:id, 'NORMAL', 0)";
                $stmtRole = $conn->prepare($sqlBuyer);
                $stmtRole->execute([':id' => $newId]);
            }

            $conn->commit();
            
            echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='/login.php';</script>";
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Lỗi đăng ký: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-4 mb-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h2 class="text-center mb-4 text-primary">Đăng Ký Tài Khoản</h2>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach($errors as $err) echo "<li>$err</li>"; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ (Lastname)</label>
                        <input type="text" class="form-control" name="lastname" required placeholder="Nguyễn">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên (Firstname)</label>
                        <input type="text" class="form-control" name="firstname" required placeholder="Văn A">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" name="username" required placeholder="Chỉ chứa chữ và số">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" required placeholder="Ít nhất 5 ký tự, gồm chữ và số">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone" required placeholder="0xxxxxxxxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CCCD/CMND (SSN)</label>
                        <input type="text" class="form-control" name="ssn" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" name="address" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Bạn muốn đăng ký vai trò gì?</label>
                    <select class="form-select" name="role">
                        <option value="buyer">Người mua (Buyer)</option>
                        <option value="seller">Người bán (Seller)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2">Đăng Ký</button>
            </form>

            <p class="text-center mt-3 mb-0">
                Đã có tài khoản? <a href="/login.php" class="text-decoration-none">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</div>

<?php include "../views/components/footer.php"; ?>