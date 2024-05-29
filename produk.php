<?php
session_start(); // Start the session to access session variables
include_once 'backend/koneksi/koneksi.php';

// Ensure the database connection is established and query the products
if ($conn) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    if (!$result) {
        echo "Error: " . $conn->error;
    }
} else {
    $result = null;
    echo "Failed to connect to the database.";
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_POST['id_product']) && isset($_SESSION['user_id'])) { // Check if user_id is set in the session
        $product_id = $_POST['id_product'];
        $user_id = $_SESSION['user_id']; // Fetch user_id from session
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

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
        echo "<script>alert('Product ID or User ID is missing.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk</title>
    <link rel="stylesheet" href="model.css">
    <!-- awesome font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <!-- Header section starts -->
    <header>
        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>
        <a href="#" class="logo">Roseskin <span>.</span></a>
        <nav class="navbar">
            <a href="TOLSKIN.php">home</a>
            <a href="about.html">about</a>
            <a href="produk.php">product</a>
        </nav>
        <div class="icons">
            <a href="#" class="fas fa-heart"></a>
            <a href="#" class="fas fa-shopping-cart"></a>
            <a href="#" class="fas fa-user"></a>
        </div>
    </header>
    <!-- Header section ends -->

    <section class="produk">
        <h1 class="heading">latest <span>products</span></h1>
        <div class="box-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="box">';
                    echo '<span class="discount">-10%</span>';
                    echo '<div class="image">';
                    echo '<img src="' . $row["gambar_product"] . '" alt="" style="width: 350px; height: 287px;"/>';
                    echo '<div class="icons">';
                    echo '<a href="#" class="fas fa-heart"></a>';
                    echo '<form method="post">';
                    // Update the key here to match the correct column name from your database
                    echo '<input type="hidden" name="id_product" value="' . $row['id_product'] . '">';
                    echo '<input type="number" name="quantity" value="1" min="1" style="width: 50px;">';
                    echo '<button type="submit" name="add_to_cart" class="cart-btn">add to cart</button>';
                    echo '</form>';
                    echo '<a href="#" class="fas fa-share"></a>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="content">';
                    echo '<h3>' . $row["nama_product"] . '</h3>';
                    echo '<div class="price">Rp' . number_format($row["harga_product"], 0, ',', '.') . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No products found.";
            }
            if (isset($conn)) {
                $conn->close();
            }
            ?>
        </div>
    </section>
</body>

</html>

</html>