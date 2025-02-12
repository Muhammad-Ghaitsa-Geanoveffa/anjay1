<?php
// Menggunakan koneksi dari koneksi.php
include 'koneksi.php';

// Ambil PenjualanID dari URL
$penjualanID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Menangani aksi tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $produkID = $_POST['produkID'];
    $jumlahProduk = $_POST['jumlahProduk'];

    // Ambil harga dan stok dari tabel produk
    $query = "SELECT Harga, Stok FROM produk WHERE ProdukID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $produkID);
    $stmt->execute();
    $stmt->bind_result($harga, $stok);
    $stmt->fetch();
    $stmt->close();

    // Cek apakah PenjualanID ada di tabel penjualan
    $checkPenjualanQuery = "SELECT COUNT(*) FROM penjualan WHERE PenjualanID = ?";
    $checkStmt = $conn->prepare($checkPenjualanQuery);
    $checkStmt->bind_param("i", $penjualanID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        if ($stok >= $jumlahProduk) {
            $subtotal = $harga * $jumlahProduk;
            $insertQuery = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Harga, Subtotal) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iiidd", $penjualanID, $produkID, $jumlahProduk, $harga, $subtotal);
            $insertStmt->execute();
            $insertStmt->close();

            // Kurangi stok produk
            $newStok = $stok - $jumlahProduk;
            $updateStokQuery = "UPDATE produk SET Stok = ? WHERE ProdukID = ?";
            $updateStmt = $conn->prepare($updateStokQuery);
            $updateStmt->bind_param("ii", $newStok, $produkID);
            $updateStmt->execute();
            $updateStmt->close();

            // Hitung ulang total harga
            updateTotalHarga($conn, $penjualanID);
        } else {
            echo "<script>alert('Stok tidak cukup!');</script>";
        }
    } else {
        echo "<script>alert('PenjualanID tidak valid!');</script>";
    }
}

// Menangani aksi edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $detailID = $_POST['detailID'];
    $jumlahBaru = $_POST['jumlahProduk'];

    // Ambil informasi produk sebelumnya
    $query = "SELECT dp.ProdukID, dp.JumlahProduk, p.Harga, p.Stok FROM detailpenjualan dp 
              JOIN produk p ON dp.ProdukID = p.ProdukID WHERE dp.DetailID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $detailID);
    $stmt->execute();
    $stmt->bind_result($produkID, $jumlahLama, $harga, $stok);
    $stmt->fetch();
    $stmt->close();

    // Hitung perubahan stok
    $stokBaru = $stok + $jumlahLama - $jumlahBaru;

    if ($stokBaru >= 0) {
        $subtotal = $harga * $jumlahBaru;
        $updateQuery = "UPDATE detailpenjualan SET JumlahProduk = ?, Subtotal = ? WHERE DetailID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ddi", $jumlahBaru, $subtotal, $detailID);
        $updateStmt->execute();
        $updateStmt->close();

        // Update stok produk
        $updateStokQuery = "UPDATE produk SET Stok = ? WHERE ProdukID = ?";
        $updateStmt = $conn->prepare($updateStokQuery);
        $updateStmt->bind_param("ii", $stokBaru, $produkID);
        $updateStmt->execute();
        $updateStmt->close();

        // Hitung ulang total harga
        updateTotalHarga($conn, $penjualanID);
    } else {
        echo "<script>alert('Stok tidak cukup!');</script>";
    }
}

// Menangani aksi delete
if (isset($_GET['delete'])) {
    $detailID = $_GET['delete'];

    // Ambil detail produk sebelum menghapus
    $query = "SELECT ProdukID, JumlahProduk FROM detailpenjualan WHERE DetailID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $detailID);
    $stmt->execute();
    $stmt->bind_result($produkID, $jumlahProduk);
    $stmt->fetch();
    $stmt->close();

    // Kembalikan stok produk
    $updateStokQuery = "UPDATE produk SET Stok = Stok + ? WHERE ProdukID = ?";
    $updateStmt = $conn->prepare($updateStokQuery);
    $updateStmt->bind_param("ii", $jumlahProduk, $produkID);
    $updateStmt->execute();
    $updateStmt->close();

    // Hapus data detail penjualan
    $deleteQuery = "DELETE FROM detailpenjualan WHERE DetailID = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $detailID);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Hitung ulang total harga
    updateTotalHarga($conn, $penjualanID);
}

// Fungsi untuk update total harga di tabel penjualan
function updateTotalHarga($conn, $penjualanID) {
    $totalQuery = $conn->query("SELECT SUM(Subtotal) AS total FROM detailpenjualan WHERE PenjualanID = $penjualanID");
    $totalData = $totalQuery->fetch_assoc();
    $totalHarga = $totalData['total'] ?? 0;

    $updateTotalQuery = "UPDATE penjualan SET TotalHarga = ? WHERE PenjualanID = ?";
    $updateStmt = $conn->prepare($updateTotalQuery);
    $updateStmt->bind_param("di", $totalHarga, $penjualanID);
    $updateStmt->execute();
    $updateStmt->close();
}

// Ambil data detail penjualan
$result = $conn->query("SELECT * FROM detailpenjualan WHERE PenjualanID = $penjualanID");

// Ambil data produk untuk dropdown
$produkResult = $conn->query("SELECT ProdukID, NamaProduk FROM produk");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-4">Detail Penjualan</h1>

<button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="document.getElementById('addModal').style.display='block'">Tambah Detail</button>

<table class="min-w-full bg-white border border-gray-300 mt-4">
    <thead>
        <tr>
            <th class="border px-4 py-2">Detail ID</th>
            <th class="border px-4 py-2">Produk ID</th>
            <th class="border px-4 py-2">Nama Produk</th>
            <th class="border px-4 py-2">Jumlah Produk</th>
            <th class="border px-4 py-2">Harga</th>
            <th class="border px-4 py-2">Subtotal</th>
            <th class="border px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td class="border px-4 py-2"><?php echo $row['DetailID']; ?></td>
                <td class="border px-4 py-2"><?php echo $row['ProdukID']; ?></td>
                <td class="border px-4 py-2">
                    <?php
                    // Ambil nama produk berdasarkan ProdukID
                    $produkQuery = "SELECT NamaProduk FROM produk WHERE ProdukID = ?";
                    $produkStmt = $conn->prepare($produkQuery);
                    $produkStmt->bind_param("i", $row['ProdukID']);
                    $produkStmt->execute();
                    $produkStmt->bind_result($namaProduk);
                    $produkStmt->fetch();
                    $produkStmt->close();
                    echo $namaProduk;
                    ?>
                </td>
                <td class="border px-4 py-2"><?php echo $row['JumlahProduk']; ?></td>
                <td class="border px-4 py-2"><?php echo number_format($row['Harga'], 2); ?></td>
                <td class="border px-4 py-2"><?php echo number_format($row['Subtotal'], 2); ?></td>
                <td class="border px-4 py-2">
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="editDetail(<?php echo $row['DetailID']; ?>, <?php echo $row['JumlahProduk']; ?>)">Edit</button>
                    <a href="?delete=<?php echo $row['DetailID']; ?>&id=<?php echo $penjualanID; ?>" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" style="display:none;">
    <div class="bg-white p-6 rounded shadow-lg">
        <h2 class="text-xl font-bold mb-4">Tambah Detail Penjualan</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label class="block mb-2">Pilih Produk</label>
                <select name="produkID" required class="border border-gray-300 p-2 w-full">
                    <option value="">-- Pilih Produk --</option>
                    <?php                         while ($produk = $produkResult->fetch_assoc()): ?>
                            <option value="<?php echo $produk['ProdukID']; ?>"><?php echo $produk['NamaProduk']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Jumlah Produk</label>
                    <input type="number" name="jumlahProduk" required class="border border-gray-300 p-2 w-full">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" onclick="document.getElementById('addModal').style.display='none'">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" style="display:none;">
        <div class="bg-white p-6 rounded shadow-lg">
            <h2 class="text-xl font-bold mb-4">Edit Detail Penjualan</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="detailID" id="editDetailID">
                <div class="mb-4">
                    <label class="block mb-2">Jumlah Produk</label>
                    <input type="number" name="jumlahProduk" id="editJumlahProduk" required class="border border-gray-300 p-2 w-full">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" onclick="document.getElementById('editModal').style.display='none'">Batal</button>
            </form>
        </div>
    </div>

    <script>
        function editDetail(detailID, jumlahProduk) {
            document.getElementById('editDetailID').value = detailID;
            document.getElementById('editJumlahProduk').value = jumlahProduk;
            document.getElementById('editModal').style.display = 'flex';
        }
    </script>
</body>
</html>