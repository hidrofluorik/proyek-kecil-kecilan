<?php
session_start();
include 'includes/db_config.php';

if(!isset($_SESSION['id_users'])) {
    die("Anda harus login untuk berkomentar.");
}

if(isset($_POST['kirim_komentar'])) {
    $id_user   = $_SESSION['id_users'];
    $id_post   = (int)$_POST['id_posts'];
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
    $teks      = mysqli_real_escape_string($koneksi, $_POST['comment_text']);
    
    // Tangkap asal halaman biar redirect-nya nggak nyasar
    $asal      = isset($_POST['asal_halaman']) ? $_POST['asal_halaman'] : 'berita';

    if(!empty($teks)) {
        $query = "INSERT INTO comments (id_posts, id_users, parent_id, comment_text) 
                  VALUES ('$id_post', '$id_user', '$parent_id', '$teks')";
        
        if(mysqli_query($koneksi, $query)) {
            // Logika Redirect
            if($asal == 'forum') {
                header("Location: detail_forum.php?id=" . $id_post . "#kolom-jawaban");
            } else {
                header("Location: detail_berita.php?id=" . $id_post . "#kolom-komentar");
            }
            exit;
        } else {
            echo "Gagal mengirim komentar: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>alert('Isian tidak boleh kosong!'); window.history.back();</script>";
    }
} else {
    header("Location: index.php");
}
?>