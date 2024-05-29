<?php
// Correct the path to the connection file
require_once('backend/koneksi/koneksi.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: backend/auth/login.php');
    exit();
}

if (isset($_POST['remove'])) {
    $productId = $_POST['productId'];
    $username = $_SESSION['username'];

    // Ensure $conn is defined and is not null
    if ($conn) {
        $removeQuery = "DELETE FROM cart WHERE product_id='$productId' AND username='$username'";
        $removeResult = mysqli_query($conn, $removeQuery);
        if ($removeResult) {
            echo "<script>alert('Product removed from cart');</script>";
        } else {
            echo "<script>alert('Failed to remove product from cart');</script>";
        }
    } else {
        echo "<script>alert('Database connection error');</script>";
    }
}

// Ensure $conn is defined and is not null before executing the query
if ($conn) {
    $cartQuery = "SELECT products.id_product, products.name, products.price, cart.quantity FROM products JOIN cart ON products.id = cart.product_id WHERE cart.username='{$_SESSION['username']}'";
    $cartResult = mysqli_query($conn, $cartQuery);
} else {
    $cartResult = false;
    echo "<script>alert('Failed to retrieve cart items');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - TOLSKIN Beauty</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-pink-100">
    <div class="container mx-auto mt-10">
        <div class="flex shadow-md my-10">
            <div class="w-3/4 bg-white px-10 py-10">
                <div class="flex justify-between border-b pb-8">
                    <h1 class="font-semibold text-2xl">Shopping Cart</h1>
                    <h2 class="font-semibold text-2xl"><?php echo $cartResult ? mysqli_num_rows($cartResult) : 0; ?> Items</h2>
                </div>
                <div class="flex mt-10 mb-5">
                    <h3 class="font-semibold text-gray-600 text-xs uppercase w-2/5">Product Details</h3>
                    <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Quantity</h3>
                    <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Price</h3>
                    <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Total</h3>
                </div>
                <?php
                $total = 0;
                if ($cartResult) {
                    while ($row = mysqli_fetch_assoc($cartResult)) {
                        $subtotal = $row['price'] * $row['quantity'];
                        $total += $subtotal;
                        echo "<div class='flex items-center hover:bg-gray-100 -mx-8 px-6 py-5'>
                            <div class='flex w-2/5'>
                                <div class='w-20'>
                                    <img class='h-24' src='path/to/image/{$row['id']}.png' alt=''>
                                </div>
                                <div class='flex flex-col justify-between ml-4 flex-grow'>
                                    <span class='font-bold text-sm'>{$row['name']}</span>
                                    <form action='' method='POST'>
                                        <input type='hidden' name='productId' value='{$row['id']}'>
                                        <button type='submit' name='remove' class='text-xs font-semibold text-red-500'>Remove</button>
                                    </form>
                                </div>
                            </div>
                            <div class='flex justify-center w-1/5'>
                                <input class='mx-2 border text-center w-8' type='text' value='{$row['quantity']}'>
                            </div>
                            <span class='text-center w-1/5 font-semibold text-sm'>{$row['price']}</span>
                            <span class='text-center w-1/5 font-semibold text-sm'>{$subtotal}</span>
                        </div>";
                    }
                }
                ?>
                <a href="produk.php" class="flex font-semibold text-indigo-600 text-sm mt-10">
                    <svg class="fill-current mr-2 text-indigo-600 w-4" viewBox="0 0 448 512"><path d="M134.059 296H24c-13.255 0-24-10.745-24-24V24C0 10.745 10.745 0 24 0h282.059c13.255 0 24 10.745 24 24v248c0 13.255-10.745 24-24 24H213.941L134.059 296zM313.941 512H404c13.255 0 24-10.745 24-24V240c0-13.255-10.745-24-24-24H131.941c-13.255 0-24 10.745-24 24v248c0 13.255 10.745 24 24 24h182.059L313.941 512z"/></svg>
                    Continue Shopping
                </a>
            </div>
            <div id="summary" class="w-1/4 px-8 py-10">
                <h1 class="font-semibold text-2xl border-b pb-8">Order Summary</h1>
                <div class="flex justify-between mt-10 mb-5">
                    <span class="font-semibold text-sm uppercase">Items <?php echo $cartResult ? mysqli_num_rows($cartResult) : 0; ?></span>
                    <span class="font-semibold text-sm"><?php echo $total; ?> IDR</span>
                </div>
                <div class="border-t mt-8">
                    <div class="flex font-semibold justify-between py-6 text-sm uppercase">
                        <span>Total cost</span>
                        <span><?php echo $total; ?> IDR</span>
                    </div>
                    <button class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full">Checkout</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
