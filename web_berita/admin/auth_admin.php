<?php
// Cek apakah session role sudah ada, kalau tidak ada atau bukan admin, tendang!
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    echo "<script>alert('Akses Ditolak! Halaman ini hanya untuk Admin.'); window.location='../index.php';</script>";
    exit;
}
?>