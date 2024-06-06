<?php
session_start();
include_once 'backend/koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        for ($i = 0; $i < count($products); $i++) {
            $product_id = $products[$i];
            $quantity = $quantities[$i];

            $sql = "INSERT INTO orders (id_product, quantity) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $product_id, $quantity);
            $stmt->execute();
        }

        // Commit transaksi
        $conn->commit();
        echo "Checkout successful!";
        
        // Kosongkan cart setelah checkout
        $_SESSION['cart'] = array();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Checkout failed: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}
?>