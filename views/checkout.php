<?php include "components/header.php"; ?>
<?php include "components/navbar.php"; ?>

<div class="container mt-5">
    <h2>Checkout</h2>

    <div class="row mt-4">

        <div class="col-md-6">
            <h5>Shipping Information</h5>

            <input class="form-control mb-3" placeholder="Full Name">
            <input class="form-control mb-3" placeholder="Address">
            <input class="form-control mb-3" placeholder="City">
            <input class="form-control mb-3" placeholder="Phone">
        </div>

        <div class="col-md-6">
            <h5>Order Summary</h5>

            <div class="border p-3 rounded">
                <p>Sample Product × 1</p>
                <hr>
                <h4>Total: $49.99</h4>

                <button class="btn btn-success w-100 mt-3">Place Order</button>
            </div>
        </div>

    </div>
</div>

<?php include "components/footer.php"; ?>
