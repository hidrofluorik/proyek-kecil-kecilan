<?php 
include '../includes/header.php'; 
include 'auth_admin.php'; 

if(isset($_POST['upload'])){
    $title   = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $content = mysqli_real_escape_string($koneksi, $_POST['konten']);
    
    // Proses Upload Gambar
    $filename = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $folder   = "../assets/img/" . $filename;

    if(move_uploaded_file($tmp_name, $folder)){
        $query = "INSERT INTO posts (title, content, gambar, is_hot) VALUES ('$title', '$content', '$filename', 0)";
        if(mysqli_query($koneksi, $query)){
            echo "<script>alert('Berita Berhasil Ditambah!'); window.location='manage_news.php';</script>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-5">
            <h2 class="fw-bold mb-4">Tulis Berita Baru</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Berita</label>
                    <input type="text" name="judul" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Gambar Utama</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Berita</label>
                    <textarea name="konten" class="form-control" rows="10" required></textarea>
                </div>
                <button type="submit" name="upload" class="btn btn-warning w-100 fw-bold py-3 shadow-sm">PUBLIKASIKAN BERITA</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>