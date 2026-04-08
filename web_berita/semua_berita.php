<?php 
include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

// Tangkap parameter filter & pencarian dari URL
$filter_tag = isset($_GET['tag']) ? mysqli_real_escape_string($koneksi, $_GET['tag']) : '';
$search_q   = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';

// Susun Query Dasar
$query_sql = "SELECT * FROM posts WHERE status='published'";

// Kalau ada pencarian
if(!empty($search_q)) {
    $query_sql .= " AND title LIKE '%$search_q%'";
}

// Kalau ada filter tag
if(!empty($filter_tag)) {
    $query_sql .= " AND tags LIKE '%$filter_tag%'";
}

// Urutkan dari yang terbaru
$query_sql .= " ORDER BY created_at DESC";

// Eksekusi Query
$query_berita = mysqli_query($koneksi, $query_sql);
?>

<style>
    body { background-color: #f8f9fa; color: #212529; }
    .card-news { transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #dee2e6; background-color: #fff; }
    .card-news:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    .img-thumb { height: 200px; object-fit: cover; border-top-left-radius: inherit; border-top-right-radius: inherit; }
    .scroll-hide::-webkit-scrollbar { display: none; }
    .scroll-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="container mt-4 mb-5">
    
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-dark">
                <?php 
                if($filter_tag) echo "Berita: #" . htmlspecialchars($filter_tag);
                elseif($search_q) echo "Hasil pencarian: '" . htmlspecialchars($search_q) . "'";
                else echo "Semua Berita Terkini"; 
                ?>
            </h3>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <form action="" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="q" class="form-control bg-white shadow-sm border-secondary-subtle" placeholder="Cari judul berita..." value="<?php echo htmlspecialchars($search_q); ?>">
                    <button class="btn btn-warning fw-bold text-dark shadow-sm" type="submit"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex overflow-auto scroll-hide gap-2 mb-4 pb-2 border-bottom border-light">
        <a href="semua_berita.php" class="btn <?php echo empty($filter_tag) ? 'btn-dark' : 'btn-outline-dark bg-white'; ?> btn-sm rounded-pill px-3 fw-bold flex-shrink-0 shadow-sm">Semua</a>
        
        <?php 
        // Ambil semua tag dari database
        $q_tags = mysqli_query($koneksi, "SELECT name FROM tags ORDER BY name ASC");
        while($t = mysqli_fetch_assoc($q_tags)):
            $nama_tag = $t['name'];
            $btn_class = ($filter_tag == $nama_tag) ? 'btn-warning text-dark' : 'btn-outline-secondary bg-white text-dark border-secondary-subtle';
        ?>
            <a href="semua_berita.php?tag=<?php echo urlencode($nama_tag); ?>" class="btn <?php echo $btn_class; ?> btn-sm rounded-pill px-3 flex-shrink-0 shadow-sm">
                #<?php echo $nama_tag; ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="row g-4">
        <?php 
        if(mysqli_num_rows($query_berita) > 0):
            while($news = mysqli_fetch_assoc($query_berita)):
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-news h-100 rounded-4 shadow-sm">
                    <img src="assets/img/<?php echo $news['gambar'] ?: 'default.jpg'; ?>" class="card-img-top img-thumb" alt="Thumbnail">
                    <div class="card-body d-flex flex-column">
                        
                        <div class="mb-2">
                            <?php 
                            // Tampilkan tag pertama saja biar rapi
                            $tags_arr = explode(',', $news['tags']);
                            $first_tag = trim($tags_arr[0]);
                            if(!empty($first_tag)):
                            ?>
                                <span class="badge bg-warning text-dark border border-warning shadow-sm">#<?php echo htmlspecialchars($first_tag); ?></span>
                            <?php endif; ?>
                            <small class="text-muted ms-2 float-end" style="font-size: 0.75rem;"><?php echo date('d M Y', strtotime($news['created_at'])); ?></small>
                        </div>

                        <h5 class="card-title fw-bold mt-2">
                            <a href="detail_berita.php?id=<?php echo $news['id_posts']; ?>" class="text-dark text-decoration-none lh-sm d-block">
                                <?php echo htmlspecialchars($news['title']); ?>
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted small flex-grow-1 mt-2">
                            <?php echo substr(strip_tags($news['content']), 0, 100); ?>...
                        </p>
                        
                        <div class="mt-3 pt-3 border-top border-secondary-subtle d-flex justify-content-between align-items-center">
                            <a href="detail_berita.php?id=<?php echo $news['id_posts']; ?>" class="text-warning text-decoration-none fw-bold small">Baca Selengkapnya →</a>
                            <span class="badge bg-success-subtle text-success border border-success">Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Tidak ada berita yang ditemukan.</h4>
                <a href="semua_berita.php" class="btn btn-outline-dark mt-3">Tampilkan Semua Berita</a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'includes/footer.php'; ?>