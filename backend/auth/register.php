<?php
include_once '../koneksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['username']; // Menggunakan 'username' dari form sebagai 'nama'
    $email = $_POST['email'];
    $password = $_POST['password']; // Using the password directly from user input

    $sql = "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $nama, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not execute. Please try again later.');</script>";
               }
        $stmt->close();
    } else {
        echo "<script>alert('Error: Could not prepare query. Please contact support.');</script>";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TOLSKIN Beauty</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-pink-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-center text-2xl font-bold text-pink-600 mb-6">Register to TOLSKIN Beauty</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
            </div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">Register</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">Already have an account? <a href="login.php" class="text-pink-600 hover:text-pink-700">Login here</a></p>
    </div>
</body>
</html>
