<?php
session_start();
include 'includes/db_config.php';

if(!isset($_SESSION['id_users'])) { echo json_encode(['success' => false]); exit; }

$id_user = $_SESSION['id_users'];
$id_posts = $_POST['id_posts'];
$vote_type = $_POST['vote_type']; // 1 atau -1

// 1. Cek apakah user sudah pernah vote di post ini
$check = mysqli_query($koneksi, "SELECT * FROM votes WHERE id_posts = '$id_posts' AND id_users = '$id_user'");

if(mysqli_num_rows($check) > 0) {
    $existing = mysqli_fetch_assoc($check);
    
    if($existing['vote_type'] == $vote_type) {
        // Jika klik tombol yang sama, hapus vote (cancel)
        mysqli_query($koneksi, "DELETE FROM votes WHERE id_posts = '$id_posts' AND id_users = '$id_user'");
    } else {
        // Jika klik tombol yang beda, update vote_type
        mysqli_query($koneksi, "UPDATE votes SET vote_type = '$vote_type' WHERE id_posts = '$id_posts' AND id_users = '$id_user'");
    }
} else {
    // Belum pernah vote, buat baru
    mysqli_query($koneksi, "INSERT INTO votes (id_posts, id_users, vote_type) VALUES ('$id_posts', '$id_user', '$vote_type')");
}

// 2. Hitung total vote baru untuk dikirim balik ke layar
$res_count = mysqli_query($koneksi, "SELECT SUM(vote_type) as total FROM votes WHERE id_posts = '$id_posts'");
$new_count = mysqli_fetch_assoc($res_count)['total'] ?? 0;

echo json_encode(['success' => true, 'new_count' => $new_count]);