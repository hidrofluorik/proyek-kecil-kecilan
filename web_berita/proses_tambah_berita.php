<?php
// 1. Paksa PHP buat nampilin error biar gak blank putih
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db_config.php';
session_start();

// 2. Cek Session 
if(!isset($_SESSION['id_users'])){
    die("Error: Anda belum login atau session id_users tidak ditemukan.");
}

if(isset($_POST['btn_simpan'])){
    $id_user = $_SESSION['id_users'];
    $title   = mysqli_real_escape_string($koneksi, $_POST['title']);
    $content = mysqli_real_escape_string($koneksi, $_POST['content']);
    $type    = mysqli_real_escape_string($koneksi, $_POST['type']); 
    
    // Logika Status: Berita butuh ACC (pending), Pertanyaan langsung (published)
    $status = ($type == 'news') ? 'pending' : 'published';

    // 3. Proses Upload Gambar
    $nama_file = "";
    if($type == 'news' && !empty($_FILES['gambar']['name'])){
        $nama_file = time() . "_" . $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $path      = "assets/img/" . $nama_file;
        
        if(!move_uploaded_file($tmp_file, $path)){
            die("Error: Gagal mengunggah gambar ke folder assets/img/.");
        }
    }

    // 4. LOGIKA SMART TAGS (MULTI-TAGS) BARU
    $tags_input = isset($_POST['tags']) ? $_POST['tags'] : '';
    $tags_bersih = []; // Nampung tag yang udah dipisah koma

    if (!empty($tags_input)) {
        $array_tags = explode(',', $tags_input);
        
        foreach($array_tags as $t) {
            $t = trim($t);
            if(!empty($t)) {
                $t_escape = mysqli_real_escape_string($koneksi, $t);
                $tags_bersih[] = $t_escape; 
                
                // Cek tag di database (pakai pengaman if)
                $cek_tag = mysqli_query($koneksi, "SELECT * FROM tags WHERE name = '$t_escape'");
                
                if($cek_tag) { // Kalau query berhasil (tabel & kolom ada)
                    if (mysqli_num_rows($cek_tag) == 0) {
                        mysqli_query($koneksi, "INSERT INTO tags (name) VALUES ('$t_escape')");
                    }
                } else {
                    // Kalau gagal, tampilkan penyebab aslinya
                    die("<strong>Error Database:</strong> Cek phpMyAdmin kamu. Pastikan tabel <b>'tags'</b> sudah dibuat dan punya kolom bernama <b>'name'</b>.<br> Pesan Error Asli: " . mysqli_error($koneksi));
                }
            }
        }
    }

    // Gabung lagi jadi string pakai koma untuk disimpan di tabel posts
    $tags_final = implode(',', $tags_bersih);

    // 5. Query INSERT POSTS
    $query = "INSERT INTO posts (id_users, title, content, gambar, type, status, tags, is_hot) 
              VALUES ('$id_user', '$title', '$content', '$nama_file', '$type', '$status', '$tags_final', 0)";
    
    $eksekusi = mysqli_query($koneksi, $query);

    if($eksekusi){
        $msg = ($status == 'pending') ? "Berita berhasil dikirim! Menunggu ACC Admin." : "Postingan berhasil diterbitkan!";
        echo "<script>alert('$msg'); window.location='index.php';</script>";
    } else {
        die("Gagal simpan postingan ke database: " . mysqli_error($koneksi));
    }
} else {
    header("Location: index.php");
}
?>