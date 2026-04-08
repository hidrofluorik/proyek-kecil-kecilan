<?php 
include '../includes/header.php'; 
include 'auth_admin.php'; 

// 1. LOGIKA UPDATE STATUS (ACC BERITA / SET HOT)
if(isset($_POST['update_status'])){
    $id = intval($_POST['id_posts']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']); 
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    
    $update = mysqli_query($koneksi, "UPDATE posts SET status = '$status', is_hot = $is_hot WHERE id_posts = $id");
    echo "<script>window.location='manage_news.php';</script>";
}

// 2. LOGIKA HAPUS (MODERASI SARA/PELANGGARAN)
if(isset($_GET['action']) && $_GET['action'] == 'delete'){
    $id = intval($_GET['id']);
    mysqli_query($koneksi, "DELETE FROM posts WHERE id_posts = $id");
    echo "<script>window.location='manage_news.php';</script>";
}
?>

<div class="container mt-5" style="min-height: 80vh;">
    
    <div class="mb-5 p-4 bg-white rounded-4 shadow-sm border-start border-warning border-5">
        <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-clock-history me-2 text-warning"></i> Menunggu Persetujuan (ACC)</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Pengirim</th>
                        <th style="width: 45%;">Judul & Preview</th>
                        <th style="width: 15%;">Tags</th>
                        <th class="text-center" style="width: 25%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // PERBAIKAN: Menggunakan u.id sebagai Primary Key tabel users
                    $q_pending = mysqli_query($koneksi, "SELECT p.*, u.username FROM posts p JOIN users u ON p.id_users = u.id WHERE p.status = 'pending' ORDER BY p.created_at DESC");
                    
                    if(!$q_pending){
                        echo "<tr><td colspan='4' class='alert alert-danger'>Error SQL: ".mysqli_error($koneksi)."</td></tr>";
                    } else {
                        if(mysqli_num_rows($q_pending) > 0){
                            while($p = mysqli_fetch_assoc($q_pending)){
                        ?>
                            <tr>
                                <td><span class="badge bg-dark px-2 py-1"><?php echo $p['username']; ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 350px;"><?php echo $p['title']; ?></div>
                                    <small class="text-muted"><?php echo substr(strip_tags($p['content']), 0, 60); ?>...</small>
                                </td>
                                <td><span class="text-primary small fw-bold">#<?php echo $row['tags'] ?? 'Umum'; ?></span></td>
                                <td class="text-center">
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="id_posts" value="<?php echo $p['id_posts']; ?>">
                                        <input type="hidden" name="status" value="published">
                                        <button type="submit" name="update_status" class="btn btn-sm btn-success px-3 fw-bold rounded-pill">
                                            <i class="bi bi-check-circle me-1"></i> ACC Rilis
                                        </button>
                                    </form>
                                    <a href="?action=delete&id=<?php echo $p['id_posts']; ?>" class="btn btn-sm btn-outline-danger px-3 rounded-pill ms-1" onclick="return confirm('Tolak & Hapus berita ini?')">
                                        Tolak
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            } 
                        } else { 
                            echo "<tr><td colspan='4' class='text-center text-muted py-4'>Tidak ada antrean berita baru.</td></tr>"; 
                        } 
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Manajemen Konten Live</h3>
            <p class="text-muted small mb-0">Atur Carousel (Hot News) dan hapus konten melanggar (SARA).</p>
        </div>
        <div class="input-group shadow-sm" style="width: 350px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="cariKonten" class="form-control border-start-0 ps-0" placeholder="Cari judul berita atau user...">
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelMaster">
                <thead class="bg-dark text-white small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th>Tipe</th>
                        <th>Judul Konten</th>
                        <th class="text-center">Carousel (Hot)</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // PERBAIKAN: Menggunakan u.id sebagai Primary Key tabel users
                    $res = mysqli_query($koneksi, "SELECT p.*, u.username FROM posts p JOIN users u ON p.id_users = u.id WHERE p.status = 'published' ORDER BY p.id_posts DESC");
                    
                    if(!$res){
                         echo "<tr><td colspan='6' class='alert alert-danger'>Error SQL: ".mysqli_error($koneksi)."</td></tr>";
                    } else {
                        while($row = mysqli_fetch_assoc($res)): 
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">#<?php echo $row['id_posts']; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['type'] == 'news') ? 'bg-info text-dark' : 'bg-secondary'; ?> px-2 py-1">
                                    <?php echo strtoupper($row['type']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $row['title']; ?></div>
                                <small class="text-muted">Penulis: <span class="text-dark fw-medium"><?php echo $row['username']; ?></span></small>
                            </td>
                            <td class="text-center">
                                <form action="" method="POST">
                                    <input type="hidden" name="id_posts" value="<?php echo $row['id_posts']; ?>">
                                    <input type="hidden" name="status" value="published">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" name="is_hot" value="1" 
                                               <?php echo ($row['is_hot']) ? 'checked' : ''; ?> 
                                               onchange="this.form.submit()" style="cursor: pointer;">
                                    </div>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-1">LIVE</span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm">
                                    <a href="../detail_berita.php?id=<?php echo $row['id_posts']; ?>" class="btn btn-sm btn-outline-dark" target="_blank" title="Lihat"><i class="bi bi-eye"></i></a>
                                    <a href="?action=delete&id=<?php echo $row['id_posts']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus konten ini secara permanen?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                        endwhile; 
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('cariKonten').addEventListener('keyup', function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tabelMaster tbody tr');
    rows.forEach(row => {
        let title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        row.style.display = title.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include '../includes/footer.php'; ?>