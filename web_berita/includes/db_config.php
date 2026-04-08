<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_berita"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

// INI YANG WAJIB ADA DAN BENAR TYPO-NYA
$base_url = "http://localhost/web_berita/"; 

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>