<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "User ID is missing. Please log in.";
    exit;
}
include_once 'backend/koneksi/koneksi.php';

$product_id = isset($_GET['id_product']) ? $_GET['id_product'] : null;

if ($conn && $product_id) {
    $sql = "SELECT * FROM products WHERE id_product = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Failed to connect to the database.";
    exit;
}

// Fetch related products
$relatedSql = "SELECT * FROM products WHERE id_product != ? LIMIT 4";
$relatedStmt = $conn->prepare($relatedSql);
$relatedStmt->bind_param("i", $product_id);
$relatedStmt->execute();
$relatedResult = $relatedStmt->get_result();
$relatedProducts = [];
if ($relatedResult->num_rows > 0) {
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedProducts[] = $row;
    }
}
$relatedStmt->close();

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['username'])) {
        $email = $_SESSION['username'];
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

        // Get the user_id from the username (email)
        $userSql = "SELECT id_user FROM users WHERE email = ?";
        $userStmt = $conn->prepare($userSql);
        $userStmt->bind_param("s", $email);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        if ($userResult->num_rows > 0) {
            $userRow = $userResult->fetch_assoc();
            $user_id = $userRow['id_user'];

            $insertSql = "INSERT INTO cart (id_user, id_product, quantity, added_on) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            if ($stmt->execute()) {
                echo "<script>alert('Product added to cart successfully!');</script>";
            } else {
                echo "<script>alert('Failed to add product to cart.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('User not found.');</script>";
        }
        $userStmt->close();
    } else {
        echo "<script>alert('User ID is missing. Please log in.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <!-- Header section starts -->
    <header class="bg-gray-800 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-2xl font-bold">TOLSKIN <span class="text-pink-500">Beauty</span></a>
            <nav class="space-x-4">
                <a href="TOLSKIN.php" class="hover:text-pink-500">Home</a>
                <a href="about.html" class="hover:text-pink-500">About</a>
                <a href="produk.php" class="hover:text-pink-500">Product</a>
                <a href="cart.php" class="hover:text-pink-500">Keranjang</a>
            </nav>
            <div class="space-x-4">
                <a href="cart.php" class="fas fa-shopping-cart hover:text-pink-500"></a>
                <a href="backend/auth/login.php" class="fas fa-user hover:text-pink-500"></a>
            </div>
        </div>
    </header>
    <!-- Header section ends -->

    <section class="py-12">
        <div class="container mx-auto flex flex-col lg:flex-row bg-white shadow-lg rounded-lg p-6">
            <div class="lg:w-1/2 flex justify-center items-center">
                <img src="<?php echo $product['gambar_product']; ?>" alt="<?php echo $product['nama_product']; ?>" class="rounded-lg max-w-full h-auto" />
            </div>
            <div class="lg:w-1/2 lg:pl-12 mt-6 lg:mt-0">
                <h1 class="text-3xl font-bold mb-4"><?php echo $product['nama_product']; ?></h1>
                <div class="text-2xl text-pink-500 font-semibold mb-4">Rp<?php echo number_format($product['harga_product'], 0, ',', '.'); ?></div>
                <p class="text-gray-700 mb-6">
                    <?php 
                    if (empty($product['deskripsi_product'])) {
                        echo "Produk kosmetik ini adalah pilihan sempurna untuk meningkatkan penampilan dan kesehatan kulit Anda. Dirancang dengan bahan-bahan alami dan diformulasikan oleh ahli dermatologi, produk ini memberikan hasil yang efektif dan aman untuk semua jenis kulit. Gunakan secara teratur untuk mendapatkan kulit yang lebih halus, cerah, dan bercahaya.";
                    } else {
                        echo $product['deskripsi_product'];
                    }
                    ?>
                </p>
                <form method="post" class="flex items-center space-x-4">
                    <input type="hidden" name="id_product" value="<?php echo $product['id_product']; ?>">
                    <div class="flex items-center border border-gray-300 rounded">
                        <button type="button" class="px-3 py-2 text-gray-700" onclick="decreaseQuantity('quantity')">-</button>
                        <input type="text" id="quantity" name="quantity" value="1" class="w-12 text-center border-l border-r border-gray-300 py-2">
                        <button type="button" class="px-3 py-2 text-gray-700" onclick="increaseQuantity('quantity')">+</button>
                    </div>
                    <button type="submit" name="add_to_cart" class="bg-pink-500 text-white px-6 py-2 rounded hover:bg-pink-600">Add to Cart</button>
                </form>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold mb-6">Produk Terkait</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($relatedProducts as $relatedProduct) { ?>
                    <div class="bg-white shadow-lg rounded-lg p-4">
                        <img src="<?php echo $relatedProduct['gambar_product']; ?>" alt="<?php echo $relatedProduct['nama_product']; ?>" class="rounded-lg w-full h-48 object-cover mb-4" />
                        <h3 class="text-lg font-semibold"><?php echo $relatedProduct['nama_product']; ?></h3>
                        <div class="text-pink-500 font-semibold">Rp<?php echo number_format($relatedProduct['harga_product'], 0, ',', '.'); ?></div>
                        <a href="detail_product.php?id_product=<?php echo $relatedProduct['id_product']; ?>" class="block bg-pink-500 text-white text-center py-2 rounded mt-4 hover:bg-pink-600">View Details</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <script>
        function increaseQuantity(id) {
            var quantityInput = document.getElementById(id);
            var currentQuantity = parseInt(quantityInput.value);
            quantityInput.value = currentQuantity + 1;
        }

        function decreaseQuantity(id) {
            var quantityInput = document.getElementById(id);
            var currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
            }
        }
    </script>
</body>

</html>
