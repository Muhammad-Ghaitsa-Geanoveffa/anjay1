<?php
session_start();

include '../koneksi.php'; // Memuat koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Cek apakah username ada di database
    $stmt = $conn->prepare("SELECT UserID, username, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verifikasi password (karena menggunakan plaintext)
        if ($password === $row["password"]) {
            $_SESSION["UserID"] = $row["UserID"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];

            header("Location: ../index.php"); // Redirect ke halaman dashboard
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
    }

    $stmt->close();
}

$conn->close();
header("Location: login.php"); // Redirect kembali ke login jika gagal
exit();
?>