<?php
$user = $_SESSION['logged_in_user'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="/index.php">E-Commerce PHP</a>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (!$user): ?>
            <li class="nav-item">
                <a class="nav-link" href="/shop.php">Shop</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cart.php">Cart</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/login.php">Login</a>
            </li>
        <?php elseif ($user['ROLE'] === 'buyer'): ?>
            <li class="nav-item">
                <a class="nav-link" href="/shop.php">Shop</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cart.php">Cart</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout.php">Logout</a>
            </li>
        <?php elseif ($user['ROLE'] === 'seller'): ?>
            <li class="nav-item">
                <a class="nav-link" href="/seller/index.php">Seller Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout.php">Logout</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
