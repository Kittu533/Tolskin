<?php
session_start();
include_once 'backend/koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        die('Username is not set in the session.');
    }

    if (isset($_POST['update'])) {
        // Update quantities in the cart
        foreach ($_POST['quantities'] as $cartId => $quantity) {
            $updateQuery = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ii", $quantity, $cartId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        echo "<script>alert('Quantities updated!');</script>";
    }

    if (isset($_POST['checkout'])) {
        // Assuming payment processing is successful
        echo "<script>alert('Checkout successful!');</script>";

        // Get user ID from email
        $email = $_SESSION['username'];
        $userQuery = "SELECT id_user FROM users WHERE email = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("s", $email);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        if ($userRow = $userResult->fetch_assoc()) {
            $userId = $userRow['id_user'];

            // Insert cart items into the order table
            $cartQuery = "SELECT id_product, quantity FROM cart WHERE id_user = ?";
            $cartStmt = $conn->prepare($cartQuery);
            $cartStmt->bind_param("i", $userId);
            $cartStmt->execute();
            $cartResult = $cartStmt->get_result();

            // Assuming $userId is correctly retrieved from the previous query
            $orderQuery = "INSERT INTO `orders` (id_user, id_product, quantity) VALUES (?, ?, ?)";
            $orderStmt = $conn->prepare($orderQuery);

            while ($cartRow = $cartResult->fetch_assoc()) {
                $orderStmt->bind_param("iii", $userId, $cartRow['id_product'], $cartRow['quantity']);
                $orderStmt->execute();
            }

            $orderStmt->close();
            $cartStmt->close();
        } else {
            echo "User not found.";
        }
        $userStmt->close();

        // Redirect or refresh the page to reflect the empty cart
        echo "<script>window.location.href = 'checkout.php';</script>";
    }
}

// Query to fetch data from cart and products
$sql = "SELECT cart.cart_id, cart.id_user, cart.id_product, cart.quantity, cart.added_on, products.nama_product, products.harga_product
        FROM cart 
        JOIN products ON cart.id_product = products.id_product";
$result = $conn->query($sql);

if (!$result) {
    echo "Failed to execute query: " . $conn->error;
    $result = new stdClass(); // Create a dummy object to prevent further errors
    $result->num_rows = 0; // Set num_rows property to 0
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }

        .btn {
            background-color: #f9c2ff;
            /* Soft pink */
            color: white;
            border-radius: 10px;
            padding: 8px 16px;
        }

        .btn:hover {
            background-color: #f3a4ff;
        }

        .input {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
        }

        .card {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 20px;
        }
    </style>
    <script>
        function updateQuantity(cartId, price, isIncrement) {
            const quantityInput = document.getElementById(`quantity-${cartId}`);
            let quantity = parseInt(quantityInput.value);
            if (isIncrement) {
                quantity++;
            } else {
                if (quantity > 1) {
                    quantity--;
                }
            }
            quantityInput.value = quantity;
            document.getElementById(`total-${cartId}`).innerText = (quantity * price).toFixed(2);
            updateTotal();
        }
    </script>

</head>

<body class="bg-pink-50">
    <div class="container mx-auto mt-10">
        <div class="flex justify-center">
            <div class="w-full card">
                <h2 class="text-2xl font-bold mb-6">Shopping Cart</h2>
                <form method="post" action="cart.php">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2">Product Name</th>
                                <th class="py-2">Price</th>
                                <th class="py-2">Quantity</th>
                                <th class="py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='border px-4 py-2'>" . $row["nama_product"] . "</td>";
                                    echo "<td class='border px-4 py-2'>" . number_format($row["harga_product"], 2) . "</td>";
                                    echo "<td class='border px-4 py-2'>";
                                    echo "<button type='button' onclick='updateQuantity(" . $row["cart_id"] . ", " . $row["harga_product"] . ", false)' class='btn'>-</button>";
                                    echo "<input type='' id='quantity-" . $row["cart_id"] . "' name='quantities[" . $row["cart_id"] . "]' value='" . $row["quantity"] . "' class='w-12 text-center'>";
                                    echo "<button type='button' onclick='updateQuantity(" . $row["cart_id"] . ", " . $row["harga_product"] . ", true)' class='btn'>+</button>";
                                    echo "</td>";
                                    echo "<td class='border px-4 py-2' id='total-" . $row["cart_id"] . "'>" . number_format($row["harga_product"] * $row["quantity"], 2) . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type="submit" name="update" class="btn">Update Quantities</button>
                    <button type="submit" name="checkout" class="btn">Checkout</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>