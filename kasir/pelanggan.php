<?php
// Menggunakan koneksi dari file koneksi.php
require_once "koneksi.php";

// Tambah Pelanggan
if (isset($_POST['add'])) {
    $namapelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomortelepon = $_POST['nomortelepon'];

    $sql = "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES ('$namapelanggan', '$alamat', '$nomortelepon')";
    $conn->query($sql);
    header("Location: pelanggan.php");
}

// Edit Pelanggan
if (isset($_POST['edit'])) {
    $id = $_POST['pelanggan_id'];
    $namapelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomortelepon = $_POST['nomortelepon'];

    $sql = "UPDATE pelanggan SET NamaPelanggan='$namapelanggan', Alamat='$alamat', NomorTelepon='$nomortelepon' WHERE PelangganID='$id'";
    $conn->query($sql);
    header("Location: pelanggan.php");
}

// Hapus Pelanggan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM pelanggan WHERE PelangganID='$id'";
    $conn->query($sql);
    header("Location: pelanggan.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan</title>
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
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Data Pelanggan</h2>

        <!-- Tombol Tambah Pelanggan -->
        <button onclick="toggleModal('addModal')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Pelanggan</button>

        <!-- Tabel Pelanggan -->
        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Nama Pelanggan</th>
                    <th class="border p-2">Alamat</th>
                    <th class="border p-2">Nomor Telepon</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM pelanggan");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='text-center'>";
                    echo "<td class='border p-2'>{$row['PelangganID']}</td>";
                    echo "<td class='border p-2'>{$row['NamaPelanggan']}</td>";
                    echo "<td class='border p-2'>{$row['Alamat']}</td>";
                    echo "<td class='border p-2'>+62" . ltrim($row['NomorTelepon'], '0') . "</td>";
                    echo "<td class='border p-2'>
                        <button onclick=\"deletePelanggan('{$row['PelangganID']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Hapus</button>
                        <button onclick=\"editModal('{$row['PelangganID']}', '{$row['NamaPelanggan']}', '{$row['Alamat']}', '" . addslashes($row['NomorTelepon']) . "')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>Edit</button>
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
                <h3 class="text-xl font-bold mb-4">Tambah Pelanggan</h3>
                <form method="POST">
                    <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" class="w-full border p-2 rounded mb-2" required>
                    <input type="text" name="alamat" placeholder="Alamat" class="w-full border p-2 rounded mb-2" required>
                    <input type="number" name="nomortelepon" placeholder="NomorTelepon" class="w-full border p-2 rounded mb-2" required>
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
                <h3 class="text-xl font-bold mb-4">Edit Pelanggan</h3>
                <form method="POST">
                    <input type="hidden" id="edit_pelanggan_id" name="pelanggan_id">
                    <input type="text" id="edit_nama_pelanggan" name="nama_pelanggan" placeholder="Nama Pelanggan" class="w-full border p-2 rounded mb-2" required>
                    <input type="text" id="edit_alamat" name="alamat" placeholder="Alamat" class="w-full border p-2 rounded mb-2" required>
                    <input type="number" id="edit_nomortelepon" name="nomortelepon" placeholder="NomorTelepon" class="w-full border p-2 rounded mb-2" required>
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

            function editModal(id, namapelanggan, alamat, nomortelepon) {
                document.getElementById("edit_pelanggan_id").value = id;
                document.getElementById("edit_nama_pelanggan").value = namapelanggan;
                document.getElementById("edit_alamat").value = alamat;
                document.getElementById("edit_nomortelepon").value = nomortelepon;
                toggleModal("editModal");
            }

            function deleteModal(id) {
                document.getElementById("delete_pelanggan_id").value = id;
                toggleModal("deleteModal");
            }

            // ini scrit untuk alert pada delete pelanggan
            function deletePelanggan(id) {
                if (confirm("Yakin ingin menghapus data pelanggan ini?")) {
                    window.location.href = "pelanggan.php?delete=" + id;
                }
            }

        </script>
</body>
</html>
