<?php
session_start();
include 'includes/db_config.php';

// Pastikan user sudah login
if(!isset($_SESSION['id_users'])) {
    echo "<script>alert('Silakan login untuk memberikan vote!'); window.history.back();</script>";
    exit();
}

if(isset($_POST['vote_type']) && isset($_POST['id_posts'])) {
    $id_user = $_SESSION['id_users'];
    $id_post = (int)$_POST['id_posts'];
    $tipe_vote = (int)$_POST['vote_type']; // 1 atau -1

    // Cek apakah user sudah pernah vote di berita ini
    $cek_vote = mysqli_query($koneksi, "SELECT * FROM votes WHERE id_posts = '$id_post' AND id_users = '$id_user'");
    
    if(mysqli_num_rows($cek_vote) > 0) {
        // Kalau sudah pernah, UPDATE vote-nya (misal awalnya upvote, dia ganti jadi downvote)
        mysqli_query($koneksi, "UPDATE votes SET vote_type = '$tipe_vote' WHERE id_posts = '$id_post' AND id_users = '$id_user'");
    } else {
        // Kalau belum pernah, INSERT vote baru
        mysqli_query($koneksi, "INSERT INTO votes (id_posts, id_users, vote_type) VALUES ('$id_post', '$id_user', '$tipe_vote')");
    }

    // Kembali ke halaman detail berita
    header("Location: detail_berita.php?id=" . $id_post);
    exit();
} else {
    header("Location: index.php");
}
?>