<?php
session_start();
if (!isset($_SESSION['invoice'])) {
    die('No invoice data found.');
}

$invoice = $_SESSION['invoice'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-center">
            <div class="w-full max-w-2xl bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Invoice</h2>
                <div class="mb-6">
                    <p><strong>Date:</strong> <?php echo $invoice['date']; ?></p>
                    <p><strong>Email:</strong> <?php echo $invoice['email']; ?></p>
                    <p><strong>Payment Method:</strong> <?php echo $invoice['payment_method']; ?></p>
                    <p><strong>Payment Status:</strong> <span
                            class="text-green-500"><?php echo $invoice['payment_status']; ?></span></p>
                </div>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">Product</th>
                            <th class="py-2">Quantity</th>
                            <th class="py-2">Price per Item</th>
                            <th class="py-2">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($invoice['order_data'] as $order) {
                            echo "<tr>";
                            echo "<td class='border px-4 py-2'>" . $order["nama_product"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $order["quantity"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . number_format($order["harga_product"], 2) . "</td>";
                            echo "<td class='border px-4 py-2'>" . number_format($order["total"], 2) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        <tr>
                            <td colspan='3' class='text-right font-bold py-2'>Total Amount</td>
                            <td class='border px-4 py-2 font-bold' id='totalKeseluruhan'>
                                <?= number_format($invoice['total_keseluruhan'], 2) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-6">
                    <a href="TOLSKIN.php"
                        class="block w-full text-center bg-pink-600 text-white py-2 rounded-lg hover:bg-pink-700">Back
                        to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>