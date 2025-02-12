<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ./login/login.php");
    exit();
}

// Ambil role pengguna dari session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-xl shadow-black/50 bg-gradient-to-b from-violet-800 to-violet-950 text-white h-screen p-9 flex flex-col border-r-4 border-black">
            <div class="p-4 text-4xl font-semibold text-white border-b font-teko text-center">Menu</div>
            <nav class="mt-4">
                
                <a onclick="location.href='index.php'" class="cursor-pointer block px-6 py-3 bg-red-300 hover:bg-gray-200 text-gray-700">Dashboard</a>
                <a onclick="location.href='produk.php'" class="cursor-pointer block px-6 py-3 hover:bg-gray-200 text-gray-700">Data Produk</a>
                <a onclick="location.href='pelanggan.php'" class="cursor-pointer block px-6 py-3 hover:bg-gray-200 text-gray-700">Pelanggan</a>
                <a onclick="location.href='penjualan.php'" class="cursor-pointer block px-6 py-3 hover:bg-gray-200 text-gray-700">Pembelian</a>
                
                <!-- Menu hanya untuk admin -->
                <?php if ($role === 'admin') : ?>
                    <a onclick="location.href='user.php'" class="cursor-pointer block px-6 py-3 hover:bg-gray-200 text-gray-700">User Management</a>
                <?php endif; ?>
                
                <a onclick="location.href='./login/logout.php'" class="cursor-pointer block px-6 py-3 bg-red-500 text-white hover:bg-red-600">Logout</a>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <main class="flex-1 p-6 bg-gray-50">
            <h1 class="text-2xl font-semibold text-gray-800">Selamat datang di Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>! (≧◡≦) ♡</h1>
            <p class="text-gray-600 mt-2">Silakan pilih menu di samping untuk mengelola data~! (ﾉ´ヮ`)ﾉ*: ･ﾟ✧</p>
        </main>
    </div>

</body>
</html>