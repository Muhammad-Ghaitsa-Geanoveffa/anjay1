<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

$UserID = $_SESSION['UserID'];

// Tambah atau Edit Data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['PenjualanID'];
    $tanggal = date('Y-m-d'); 
    $pelanggan = $_POST['PelangganID'];

    if ($id) {
        $query = "UPDATE penjualan SET TanggalPenjualan='$tanggal', PelangganID='$pelanggan' WHERE PenjualanID='$id'";
    } else {
        $query = "INSERT INTO penjualan (UserID, TanggalPenjualan, PelangganID) VALUES ('$UserID', '$tanggal', '$pelanggan')";
    }
    mysqli_query($conn, $query);
    header("Location: penjualan.php");
    exit;
}

// Hapus Data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM penjualan WHERE PenjualanID='$id'");
    header("Location: penjualan.php");
    exit;
}

// Ambil data penjualan
$query = "SELECT p.*, pl.NamaPelanggan, u.username FROM penjualan p 
          JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
          JOIN user u ON p.UserID = u.UserID";
$result = mysqli_query($conn, $query);

// Ambil data pelanggan untuk dropdown
$query_pelanggan = "SELECT * FROM pelanggan";
$result_pelanggan = mysqli_query($conn, $query_pelanggan);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="">


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


        <div class="flex-1 p-6">
    <h2 class="text-2xl font-bold mb-4">Data Penjualan</h2>
    <button class="bg-blue-500 text-white px-4 py-2 mb-4" onclick="toggleModal('modalTambah')">Tambah Penjualan</button>
    
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Tanggal</th>
                <th class="border px-4 py-2">Pelanggan</th>
                <th class="border px-4 py-2">User </th>
                <th class="border px-4 py-2">Total Harga</th> <!-- Kolom baru -->
                <th class="border px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['PenjualanID']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['TanggalPenjualan']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['NamaPelanggan']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['username']; ?></td>
                    <td class="border px-4 py-2"><?php echo number_format($row['TotalHarga'], 2); ?></td> <!-- Tampilkan Total Harga -->
                    <td class="border px-4 py-2">
                        <button class="bg-yellow-500 text-white px-3 py-1" onclick="editData(<?php echo $row['PenjualanID']; ?>, <?php echo $row['PelangganID']; ?>)">Edit</button>
                        <a href="?hapus=<?php echo $row['PenjualanID']; ?>" class="bg-red-500 text-white px-3 py-1">Hapus</a>
                        <a href="detailpenjualan.php?id=<?php echo $row['PenjualanID']; ?>" class="bg-green-500 text-white px-3 py-1">Detail</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal Tambah -->
    <div id="modalTambah" class="fixed inset-0 hidden bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg">
            <h3 class="text-xl mb-4">Tambah Penjualan</h3>
            <form action="penjualan.php" method="post">
                <label>Pelanggan:</label>
                <select name="PelangganID" class="border p-2 w-full">
                    <?php while ($pelanggan = mysqli_fetch_assoc($result_pelanggan)) : ?>
                        <option value="<?php echo $pelanggan['PelangganID']; ?>"><?php echo $pelanggan['NamaPelanggan']; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 mt-2">Simpan</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 mt-2" onclick="toggleModal('modalTambah')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modalEdit" class="fixed inset-0 hidden bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg">
            <h3 class="text-xl mb-4">Edit Penjualan</h3>
            <form action="penjualan.php" method="post">
                <input type="hidden" name="PenjualanID" id="editPenjualanID">
                <label>Pelanggan:</label>
                <select name="PelangganID" id="editPelangganID" class="border p-2 w-full"></select>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 mt-2">Simpan</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 mt-2" onclick="toggleModal('modalEdit')">Batal</button>
            </form>
        </div>
    </div>
    </div>
</div>

    <script>
        function toggleModal(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function editData(id, pelanggan) {
            document.getElementById('editPenjualanID').value = id;
            document.getElementById('editPelangganID').value = pelanggan;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>
