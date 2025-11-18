<?php
session_start();
include "../views/components/header.php";
include "../views/components/navbar.php";

$errors = [];

// --- Demo 2 user ---
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        'U001' => [
            'USERID' => 'U001',
            'SSN' => '123456789',
            'USERNAME' => 'buyer01',
            'USER_PASSWORD' => password_hash('buyer123', PASSWORD_DEFAULT),
            'FIRSTNAME' => 'John',
            'LASTNAME' => 'Buyer',
            'ADDRESS' => '123 Main St',
            'PHONE' => '0123456789',
            'ROLE' => 'buyer'
        ],
        'U002' => [
            'USERID' => 'U002',
            'SSN' => '987654321',
            'USERNAME' => 'seller01',
            'USER_PASSWORD' => password_hash('seller123', PASSWORD_DEFAULT),
            'FIRSTNAME' => 'Demo',
            'LASTNAME' => 'Seller',
            'ADDRESS' => '456 Market St',
            'PHONE' => '0987654321',
            'ROLE' => 'seller',
            'SELLERID' => 'S001'
        ]
    ];
}

// --- Auto-login nếu có cookie ---
if (isset($_COOKIE['remember_username']) && isset($_COOKIE['remember_token'])) {
    $username = $_COOKIE['remember_username'];
    $token = $_COOKIE['remember_token'];
    foreach ($_SESSION['users'] as $user) {
        if ($user['USERNAME'] === $username && hash('sha256', $user['USER_PASSWORD']) === $token) {
            $_SESSION['logged_in_user'] = $user;
            header("Location: " . ($user['ROLE'] === 'seller' ? "/seller/index.php" : "/shop.php"));
            exit;
        }
    }
}

// --- Xử lý form login ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $found = false;
    foreach ($_SESSION['users'] as $user) {
        if ($user['USERNAME'] === $username) {
            $found = true;
            if (!password_verify($password, $user['USER_PASSWORD'])) {
                $errors[] = "Incorrect password.";
            } else {
                $_SESSION['logged_in_user'] = $user;

                // Nếu chọn nhớ đăng nhập
                if ($remember) {
                    setcookie('remember_username', $username, time() + 30*24*3600, "/");
                    setcookie('remember_token', hash('sha256', $user['USER_PASSWORD']), time() + 30*24*3600, "/");
                }

                header("Location: " . ($user['ROLE'] === 'seller' ? "/seller/index.php" : "/shop.php"));
                exit;
            }
        }
    }
    if (!$found) $errors[] = "User not found.";
}
?>

<div class="container mt-5" style="max-width: 450px;">
    <h2 class="text-center mb-4">Login</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input class="form-control mb-3" type="text" name="username" placeholder="Username" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3">
        No account? <a href="/register.php">Register</a>
    </p>
</div>

<?php include "../views/components/footer.php"; ?>
