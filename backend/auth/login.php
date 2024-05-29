<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TOLSKIN Beauty</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-pink-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-center text-2xl font-bold text-pink-600 mb-6">Login to TOLSKIN Beauty</h2>
        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
            </div>
            <button type="submit" name="login" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">Login</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">Don't have an account? <a href="register.php" class="text-pink-600 hover:text-pink-700">Register here</a></p>
    </div>
</body>
</html>

<?php
include('../koneksi/koneksi.php');

if(isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Encrypt the password before checking it against the database
    $encrypted_password = md5($password); // or use password_hash() and password_verify() for better security

    // Ensure the column names in the SQL query match your database schema
    $query = "SELECT * FROM users WHERE nama='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    $check = mysqli_num_rows($result);

    if($check > 0) {
        session_start();
        $_SESSION['username'] = $username;
        header('Location: ../../TOLSKIN.php');
    } else {
        echo "<script>alert('Username or Password is incorrect.');</script>";
    }
}
?>