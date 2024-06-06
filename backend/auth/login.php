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

                <label for="username" class="block text-sm font-medium text-gray-700">Email:</label>

                <input type="email" id="username" name="username" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">

            </div>

            <div>

                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>

                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">

            </div>

            <button type="submit" name="login"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">Login</button>

        </form>

        <p class="mt-4 text-center text-sm text-gray-600">Don't have an account? <a href="register.php"
                class="text-pink-600 hover:text-pink-700">Register here</a></p>

    </div>

</body>



</html>



<?php
include_once '../koneksi/koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query untuk mendapatkan password hash dari database
    $query = "SELECT password FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $email; // Simpan email ke dalam session
        header('Location: ../../TOLSKIN.php');
    } else {
        echo "<script>alert('Email or Password is incorrect.');</script>";
    }
}
?>