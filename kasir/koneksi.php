<?php
$host = "localhost"; // Ganti jika database ada di server lain
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "kasir"; // Nama database yang digunakan

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    echo "";
}
?>