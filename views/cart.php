<?php
session_start();

// Giả sử cart lưu dạng:
// $_SESSION['cart'] = [
//     'P001' => ['id'=>'P001','name'=>'T-Shirt','price'=>19.99,'qty'=>2],
//     'P002' => ['id'=>'P002','name'=>'Sneakers','price'=>49.99,'qty'=>1]
// ];

// Xử lý remove sản phẩm
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
    }
    header("Location: cart.php");
    exit;
}

// Xử lý cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if (isset($_SESSION['cart'][$id])) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $_SESSION['cart'][$id]['qty'] = $qty;
            } else {
                unset($_SESSION['cart'][$id]); // Xóa nếu qty <= 0
            }
        }
    }
    header("Location: cart.php");
    exit;
}

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalPrice = 0;
?>

<?php include "components/header.php"; ?>
<?php include "components/navbar.php"; ?>

<div class="container mt-5">
    <h2>Your Cart</h2>

    <form method="post">
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cartItems): ?>
                    <?php foreach($cartItems as $item): ?>
                        <?php $lineTotal = $item['price'] * $item['qty']; ?>
                        <?php $totalPrice += $lineTotal; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>$<?= number_format($item['price'],2) ?></td>
                            <td>
                                <input type="number" name="qty[<?= $item['id'] ?>]" class="form-control w-50" value="<?= $item['qty'] ?>" min="0">
                            </td>
                            <td>$<?= number_format($lineTotal,2) ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($cartItems): ?>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary">Update Cart</button>
                <h3>Total: $<?= number_format($totalPrice,2) ?></h3>
            </div>
            <div class="text-end mt-3">
                <a href="/checkout.php" class="btn btn-success btn-lg">Checkout</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include "components/footer.php"; ?>
