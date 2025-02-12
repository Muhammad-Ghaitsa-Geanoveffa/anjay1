<?php
// Menggunakan koneksi dari file koneksi.php
require_once "koneksi.php";

// Tambah Produk
if (isset($_POST['add'])) {
    $namabarang = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $sql = "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES ('$namabarang', '$harga', '$stok')";
    $conn->query($sql);
    header("Location: produk.php");
}

// Edit Produk
if (isset($_POST['edit'])) {
    $id = $_POST['produk_id'];
    $namabarang = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $sql = "UPDATE produk SET NamaProduk='$namabarang', Harga='$harga', Stok='$stok' WHERE ProdukID='$id'";
    $conn->query($sql);
    header("Location: produk.php");
}

// Hapus Produk
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM produk WHERE ProdukID='$id'";
    $conn->query($sql);
    header("Location: produk.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 ">

<div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md">
            <div class="p-4 text-xl font-bold text-gray-700 border-b">Admin Panel</div>
            <nav class="mt-4">
                <a onclick="location.href='index.php'" class="block px-6 py-3 bg-red-300 hover:bg-gray-200 text-gray-700">Dashboard</a>
                <a onclick="location.href='produk.php'" class="block px-6 py-3 hover:bg-gray-200 text-gray-700">Data Produk</a>
                <a onclick="location.href='pelanggan.php'" class="block px-6 py-3 hover:bg-gray-200 text-gray-700">Pelanggan</a>
                <a onclick="location.href='penjualan.php'" class="block px-6 py-3 hover:bg-gray-200 text-gray-700">Pembelian</a>
                <a href="user.php" class="block px-6 py-3 hover:bg-gray-200 text-gray-700">User Management</a>
                <a href="logout.php" class="block px-6 py-3 bg-red-500 text-white hover:bg-red-600">Logout</a>
            </nav>
        </aside>


    <div class="flex-1 mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Data Produk</h2>

        <!-- Tombol Tambah Produk -->
        <button onclick="toggleModal('addModal')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Produk</button>

        <!-- Tabel Produk -->
        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Nama Produk</th>
                    <th class="border p-2">Harga</th>
                    <th class="border p-2">Stok</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM produk");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='text-center'>";
                    echo "<td class='border p-2'>{$row['ProdukID']}</td>";
                    echo "<td class='border p-2'>{$row['NamaProduk']}</td>";
                    echo "<td class='border p-2'>Rp" . number_format($row['Harga'], 2, ',', '.') . "</td>";
                    echo "<td class='border p-2'>{$row['Stok']}</td>";
                    echo "<td class='border p-2'>
                    <button onclick=\"deleteProduk('{$row['ProdukID']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Hapus</button>
                    <button onclick=\"editModal('{$row['ProdukID']}', '{$row['NamaProduk']}', '{$row['Harga']}', '{$row['Stok']}')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>Edit</button>
                </td>";
                
                
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div id="addModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-96">
            <h3 class="text-xl font-bold mb-4">Tambah Produk</h3>
            <form method="POST">
                <input type="text" name="nama_produk" placeholder="Nama Produk" class="w-full border p-2 rounded mb-2" required>
                <input type="number" name="harga" placeholder="Harga" class="w-full border p-2 rounded mb-2" required>
                <input type="number" name="stok" placeholder="Stok" class="w-full border p-2 rounded mb-2" required>
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="toggleModal('addModal')" class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="submit" name="add" class="px-4 py-2 bg-blue-500 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-96">
            <h3 class="text-xl font-bold mb-4">Edit Produk</h3>
            <form method="POST">
                <input type="hidden" id="edit_produk_id" name="produk_id">
                <input type="text" id="edit_nama_produk" name="nama_produk" placeholder="Nama Produk" class="w-full border p-2 rounded mb-2" required>
                <input type="number" id="edit_harga" name="harga" placeholder="Harga" class="w-full border p-2 rounded mb-2" required>
                <input type="number" id="edit_stok" name="stok" placeholder="Stok" class="w-full border p-2 rounded mb-2" required>
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="toggleModal('editModal')" class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="submit" name="edit" class="px-4 py-2 bg-yellow-500 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    </div>


    <script>
        function toggleModal(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle("hidden");
            } else {
                console.error("Modal dengan ID " + id + " tidak ditemukan!");
            }
        }

        function editModal(id, nama, harga, stok) {
            document.getElementById("edit_produk_id").value = id;
            document.getElementById("edit_nama_produk").value = nama;
            document.getElementById("edit_harga").value = harga;
            document.getElementById("edit_stok").value = stok;
            toggleModal("editModal");
        }

        function deleteModal(id) {
            document.getElementById("delete_produk_id").value = id;
            toggleModal("deleteModal");
        }

        // ini scrit untuk alert pada delete produk
        function deleteProduk(id) {
            if (confirm("Yakin ingin menghapus produk ini?")) {
                window.location.href = "produk.php?delete=" + id;
            }
        }

    </script>
</body>
</html>
