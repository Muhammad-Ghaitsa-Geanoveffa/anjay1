<?php
$error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
unset($_SESSION['error']); // Hapus error setelah ditampilkan
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-blue-500 to-purple-400 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-lg p-8 w-96">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <?php if ($error) : ?>
            <p class="text-red-500 text-sm text-center mb-4"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="login_proses.php">
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" required class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Login</button>
        </form>
    </div>

</body>
</html>
