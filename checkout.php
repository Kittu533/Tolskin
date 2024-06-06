<?php
session_start();
include_once 'backend/koneksi/koneksi.php';

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    die('Username is not set in the session.');
}

$email = $_SESSION['username'];
$userQuery = "SELECT id_user FROM users WHERE email = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $email);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userId = $userResult->fetch_assoc()['id_user'];

$cartQuery = "SELECT cart.id_product, products.nama_product, products.harga_product, cart.quantity FROM cart JOIN products ON cart.id_product = products.id_product WHERE cart.id_user = ?";
$cartStmt = $conn->prepare($cartQuery);
$cartStmt->bind_param("i", $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

$totalKeseluruhan = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paymentMethod = $_POST['paymentMethod'];
    $orderData = [];

    $conn->begin_transaction();
    try {
        $orderQuery = "INSERT INTO orders (id_user, id_product, quantity) VALUES (?, ?, ?)";
        $orderStmt = $conn->prepare($orderQuery);

        while ($row = $cartResult->fetch_assoc()) {
            $orderStmt->bind_param("iii", $userId, $row['id_product'], $row['quantity']);
            $orderStmt->execute();
            $total = $row["harga_product"] * $row["quantity"];
            $totalKeseluruhan += $total;

            // Store the order data in session
            $orderData[] = [
                'nama_product' => $row['nama_product'],
                'harga_product' => $row['harga_product'],
                'quantity' => $row['quantity'],
                'total' => $total
            ];
        }

        // Commit transaksi
        $conn->commit();

        // Clear the cart after successful order
        $clearCartQuery = "DELETE FROM cart WHERE id_user = ?";
        $clearCartStmt = $conn->prepare($clearCartQuery);
        $clearCartStmt->bind_param("i", $userId);
        $clearCartStmt->execute();

        // Store order details in session
        $_SESSION['invoice'] = [
            'date' => date("Y-m-d H:i:s"),
            'email' => $email,
            'payment_method' => $paymentMethod,
            'payment_status' => "Berhasil dibayar",
            'order_data' => $orderData,
            'total_keseluruhan' => $totalKeseluruhan
        ];

        // Redirect to invoice page
        header("Location: invoice.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Checkout failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-pink-50">
    <div class="container mx-auto mt-10">
        <div class="flex justify-center">
            <div class="w-full card">
                <h2 class="text-2xl font-bold mb-6">Checkout</h2>
                <form method="post">
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
                            $cartStmt->execute();
                            $cartResult = $cartStmt->get_result();
                            while ($row = $cartResult->fetch_assoc()) {
                                $total = $row["harga_product"] * $row["quantity"];
                                $totalKeseluruhan += $total;
                                echo "<tr>";
                                echo "<td class='border px-4 py-2'>" . $row["nama_product"] . "</td>";
                                echo "<td class='border px-4 py-2'>" . number_format($row["harga_product"], 2) . "</td>";
                                echo "<td class='border px-4 py-2'>" . $row["quantity"] . "</td>";
                                echo "<td class='border px-4 py-2'>" . number_format($total, 2) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                            <tr>
                                <td colspan='3' class='text-right font-bold py-2'>Total Keseluruhan</td>
                                <td class='border px-4 py-2 font-bold' id='totalKeseluruhan'>
                                    <?= number_format($totalKeseluruhan, 2) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <label for="paymentMethod" class="block text-sm font-medium text-gray-700">Payment
                            Method:</label>
                        <select id="paymentMethod" name="paymentMethod" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-pink-600 text-white py-2 rounded-lg">Proceed to
                            Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>