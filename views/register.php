<?php
session_start();
include "../views/components/header.php";
include "../views/components/navbar.php";

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($_SESSION['users'][$email])) {
        $errors[] = "User already exists.";
    } else {
        $_SESSION['users'][$email] = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'buyer',
            'name' => $name
        ];
        header("Location: /login.php");
        exit;
    }
}
?>

<div class="container mt-5" style="max-width: 450px;">
    <h2 class="text-center mb-4">Register</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input class="form-control mb-3" type="text" name="name" placeholder="Name" required>
        <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn btn-success w-100">Register</button>
    </form>

    <p class="text-center mt-3">
        Already have account? <a href="/login.php">Login</a>
    </p>
</div>

<?php include "../views/components/footer.php"; ?>
