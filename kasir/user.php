<?php

session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['role'])) {
    header("Location: .login/login.php"); // Redirect ke halaman login jika belum login
    exit();
}

// Cek apakah user memiliki level 'admin'
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirect ke halaman utama jika bukan admin
    exit();
}


// Tambah user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $stmt->close();
    header("Location: user.php");
    exit();
}

// Edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE user SET username = ?, password = ?, role = ? WHERE userID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $username, $password, $role, $userID);
    } else {
        $query = "UPDATE user SET username = ?, role = ? WHERE userID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $username, $role, $userID);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: user.php");
    exit();
}

// Hapus user
if (isset($_GET['delete'])) {
    $userID = $_GET['delete'];
    $query = "DELETE FROM user WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();
    header("Location: user.php");
    exit();
}


$result = $conn->query("SELECT * FROM user");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Manajemen User</h2>
    <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="openModal('createModal')">Tambah User</button>
    
    <table class="w-full bg-white shadow-md rounded mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">#</th>
                <th class="p-2">Username</th>
                <th class="p-2">Role</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr class="border-t">
                    <td class="p-2"><?= $row['userID'] ?></td>
                    <td class="p-2"><?= $row['username'] ?></td>
                    <td class="p-2"><?= ucfirst($row['role']) ?></td>
                    <td class="p-2">
                        <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="editUser('<?= $row['userID'] ?>', '<?= $row['username'] ?>', '<?= $row['role'] ?>')">Edit</button>
                        <a href="user.php?delete=<?= $row['userID'] ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Hapus user ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Create -->
<div id="createModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded w-96">
        <h3 class="text-xl mb-3">Tambah User</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="username" placeholder="Username" class="w-full p-2 border rounded mb-3" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-2 border rounded mb-3" required>
            <select name="role" class="w-full p-2 border rounded mb-3" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah</button>
            <button type="button" class="ml-2" onclick="closeModal('createModal')">Batal</button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded w-96">
        <h3 class="text-xl mb-3">Edit User</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="userID" id="editUserID">
            <input type="text" name="username" id="editUsername" class="w-full p-2 border rounded mb-3" required>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full p-2 border rounded mb-3">
            <select name="role" id="editRole" class="w-full p-2 border rounded mb-3" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
            <button type="button" class="ml-2" onclick="closeModal('editModal')">Batal</button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove("hidden");
}
function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
}
function editUser(id, username, role) {
    document.getElementById("editUserID").value = id;
    document.getElementById("editUsername").value = username;
    document.getElementById("editRole").value = role;
    openModal('editModal');
}
</script>
</body>
</html>
