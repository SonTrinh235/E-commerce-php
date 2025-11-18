document.addEventListener("DOMContentLoaded", () => {
    const goTo = (path) => window.location.href = path;

    const btnShop = document.querySelector("#btnShop");
    const btnCart = document.querySelector("#btnCart");
    const btnLogin = document.querySelector("#btnLogin");

    if (btnShop) btnShop.addEventListener("click", () => goTo("/shop.php"));
    if (btnCart) btnCart.addEventListener("click", () => goTo("/cart.php"));
    if (btnLogin) btnLogin.addEventListener("click", () => goTo("/login.php"));
});


function addToCart(product) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    cart.push(product);

    localStorage.setItem("cart", JSON.stringify(cart));

    alert("Added to cart!");
}
