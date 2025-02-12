<?php
include 'koneksi.php'; // Pastikan file koneksi tersedia

// Tambah user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Simpan password tanpa hashing
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
    
    // Jika password diisi, update password juga
    if (!empty($_POST['password'])) {
        $password = $_POST['password']; // Simpan password tanpa hashing
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

// Cek apakah user memiliki level 'admin'
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirect ke halaman utama jika bukan admin
    exit();
}

// Ambil data user untuk ditampilkan
$result = $conn->query("SELECT * FROM user");
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Manajemen User</h2>

    <!-- Form Tambah User -->
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Role:</label>
            <select name="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Tambah User</button>
    </form>

    <hr>

    <!-- Tabel User -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['userID'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td>
                        <a href="#" class="btn btn-warning btn-sm" onclick="editUser('<?= $row['userID'] ?>', '<?= $row['username'] ?>', '<?= $row['role'] ?>')">Edit</a>
                        <a href="user.php?delete=<?= $row['userID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" onclick="closeModal()"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="userID" id="editUserID">
                    <div class="mb-3">
                        <label>Username:</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password (kosongkan jika tidak ingin mengubah):</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Role:</label>
                        <select name="role" id="editRole" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editUser(id, username, role) {
    document.getElementById("editUserID").value = id;
    document.getElementById("editUsername").value = username;
    document.getElementById("editRole").value = role;
    document.getElementById("editModal").style.display = "block";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}
</script>

</body>
</html>
