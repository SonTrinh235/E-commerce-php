<?php
$user = $_SESSION['logged_in_user'] ?? null;
$role = $user['role'] ?? ''; 
?>

<nav class="navbar navbar-expand-lg navbar-light mb-4 shadow-sm" style="background-color: #e3f2fd;">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="/index.php">
        <i class="bi bi-shop"></i> E-commerce-php
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="/index.php">Trang chủ</a></li>
        
        <?php if (!$user || $role === 'buyer'): ?>
            <li class="nav-item"><a class="nav-link" href="/shop.php">Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="/cart.php">Giỏ hàng</a></li>
        <?php elseif ($role === 'seller'): ?>
            <li class="nav-item"><a class="nav-link fw-bold text-primary" href="/seller/index.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/seller/products.php">Quản lý Sản phẩm</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (!$user): ?>
            <li class="nav-item"><a class="nav-link" href="/login.php">Đăng nhập</a></li>
            <li class="nav-item"><a class="btn btn-primary ms-2 text-white" href="/register.php">Đăng ký</a></li>
        <?php else: ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></strong>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person-vcard me-2"></i>Hồ sơ</a></li>
                    
                    <?php if ($role === 'seller'): ?>
                        <li><a class="dropdown-item" href="/seller/add-product.php"><i class="bi bi-plus-circle me-2"></i>Thêm sản phẩm</a></li>
                    <?php endif; ?>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                </ul>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>