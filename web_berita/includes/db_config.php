<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_berita"; // <-- GANTI DENGAN NAMA DATABASE KAMU!

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Session dimulai di sini satu kali saja untuk semua halaman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>