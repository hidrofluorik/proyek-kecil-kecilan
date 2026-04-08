<?php 
// Nyalakan error sementara (Biar gampang debug kalau ada typo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

// ==========================================
// 1. LOGIKA FILTER TAG & TYPE (ANTI-BOCOR)
// ==========================================
$syarat_tag = "";
$minat_user = [];

if(isset($_SESSION['id_users'])) {
    $id_usr = $_SESSION['id_users'];
    $q_minat = mysqli_query($koneksi, "SELECT tag_name FROM user_interests WHERE id_users = '$id_usr'");
    
    if($q_minat) {
        while($row = mysqli_fetch_assoc($q_minat)) {
            $tag_bersih = mysqli_real_escape_string($koneksi, trim($row['tag_name']));
            $minat_user[] = "p.tags LIKE '%$tag_bersih%'"; 
        }

        if(count($minat_user) > 0) {
            $gabungan_minat = implode(" OR ", $minat_user);
            $syarat_tag = " AND ($gabungan_minat) ";
        }
    }
}

// QUERY UTAMA UNTUK LIST BERITA (Hanya News, Bukan Question)
$query_news_utama = "SELECT p.*, u.username 
                     FROM posts p 
                     JOIN users u ON p.id_users = u.id 
                     WHERE p.status = 'published' 
                     AND p.type != 'question' $syarat_tag 
                     ORDER BY p.created_at DESC LIMIT 6";
?>

<style>
/* --- CAROUSEL FULL WIDTH FIX --- */
#hot-news {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow: hidden;
    background-color: #000;
}
#carouselUtama .carousel-item img {
    width: 100%;
    height: 600px; 
    object-fit: cover;
    filter: brightness(0.7);
}

/* --- FIX KLIK & POSISI --- */
.container.mt-4 { position: relative; z-index: 10; }
#btn-tulis { position: relative; z-index: 1050 !important; cursor: pointer; }
.carousel-caption { padding-bottom: 80px; text-align: left; }
.carousel-caption h2 { font-weight: 800; text-transform: uppercase; }
.card-news { transition: all 0.3s ease; border: none !important; }
.card-news:hover { transform: translateY(-5px); }
.img-news-thumb { height: 100%; min-height: 180px; object-fit: cover; }
.modal-content input::placeholder, .modal-content textarea::placeholder { color: #8c9297 !important; }
.modal-content input, .modal-content textarea { color: #ffffff !important; }
</style>

<section id="hot-news" class="mb-5">
    <div id="carouselUtama" class="carousel slide shadow-lg" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php 
            // Filter: Hanya Hot News yang BUKAN Question
            $q_ind = mysqli_query($koneksi, "SELECT * FROM posts WHERE is_hot = 1 AND type != 'question' LIMIT 5");
            if($q_ind) {
                $i = 0;
                while($ind = mysqli_fetch_assoc($q_ind)){
                    $act = ($i == 0) ? 'class="active"' : '';
                    echo '<button type="button" data-bs-target="#carouselUtama" data-bs-slide-to="'.$i.'" '.$act.'></button>';
                    $i++;
                }
            }
            ?>
        </div>

        <div class="carousel-inner">
            <?php 
            // Filter: Hanya Hot News yang BUKAN Question
            $query_hot = mysqli_query($koneksi, "SELECT * FROM posts WHERE is_hot = 1 AND type != 'question' ORDER BY created_at DESC LIMIT 5");
            $active = "active"; 
            if($query_hot && mysqli_num_rows($query_hot) > 0):
                while($hot = mysqli_fetch_assoc($query_hot)): 
            ?>
                <div class="carousel-item <?php echo $active; ?>" data-bs-interval="5000">
                    <img src="assets/img/<?php echo $hot['gambar'] ?: 'default.jpg'; ?>" class="d-block w-100" alt="Hot News">
                    <div class="carousel-caption">
                        <div class="container"> 
                            <span class="badge bg-warning text-dark mb-3 px-3 py-2 fw-bold">
                                <i class="bi bi-fire me-1"></i> HOT NEWS
                            </span>
                            <h2 class="display-6 text-white"><?php echo $hot['title']; ?></h2>
                            <p class="opacity-75 d-none d-md-block text-white"><?php echo substr(strip_tags($hot['content']), 0, 150); ?>...</p>
                            <a href="detail_berita.php?id=<?php echo $hot['id_posts']; ?>" class="btn btn-warning fw-bold px-4 py-2 mt-2">BACA SELENGKAPNYA</a>
                        </div>
                    </div>
                </div>
            <?php $active = ""; endwhile; else: ?>
                <div class="carousel-item active">
                    <img src="assets/img/default_carousel.jpg" class="d-block w-100" style="filter: grayscale(1);">
                    <div class="carousel-caption text-center">
                        <h5>Belum Ada Berita Utama</h5>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselUtama" data-bs-slide="prev" style="z-index: 20;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselUtama" data-bs-slide="next" style="z-index: 20;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div> 
</section>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4 border-bottom pb-3" style="position: relative; z-index: 20;">
                <h4 class="fw-bold mb-0 me-3">Berita Terbaru</h4>
                
                <?php if(isset($_SESSION['id_users'])): ?>
                    <button type="button" id="btn-tulis" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold border-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPostingan">
                        <i class="bi bi-pencil-square me-1"></i> Tulis Sesuatu
                    </button>
                <?php endif; ?>

                <a href="semua_berita.php" class="ms-auto text-decoration-none text-warning fw-bold small">Lihat Semua →</a>
            </div>
            
            <?php 
            // Eksekusi Query News Utama yang sudah kita filter di atas
            $sql_news = mysqli_query($koneksi, $query_news_utama);            
            if($sql_news && mysqli_num_rows($sql_news) > 0):
                while($news = mysqli_fetch_assoc($sql_news)):
            ?>
            <div class="card card-news mb-4 shadow-sm overflow-hidden rounded-3">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="assets/img/<?php echo isset($news['gambar']) && $news['gambar'] != '' ? $news['gambar'] : 'default.jpg'; ?>" class="img-news-thumb w-100">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">
                                <a href="detail_berita.php?id=<?php echo $news['id_posts']; ?>" class="text-dark text-decoration-none"><?php echo $news['title']; ?></a>
                            </h5>
                            <p class="card-text text-muted small mb-3"><?php echo substr(strip_tags($news['content']), 0, 120); ?>...</p>
                            <div class="d-flex justify-content-between align-items-end mt-auto">
                                <div class="d-flex flex-wrap gap-1">
                                    <?php 
                                    $raw_tags = isset($news['tags']) && $news['tags'] != '' ? $news['tags'] : 'Berita';
                                    $tag_array = explode(',', $raw_tags);
                                    foreach($tag_array as $t): 
                                        $t = trim($t);
                                        if(!empty($t)):
                                    ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary small">
                                            #<?php echo htmlspecialchars($t); ?>
                                        </span>
                                    <?php endif; endforeach; ?>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success ms-2">Verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
                <p class="text-center text-muted my-5">Belum ada berita terbaru.</p>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <h4 class="fw-bold mb-4">Referensi Untukmu</h4>
            <div class="card shadow-sm border-0 bg-white p-4 mb-4 rounded-3 text-center">
                <?php if(isset($_SESSION['username'])): ?>
                    <div class="bg-warning text-dark rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold" style="width:50px; height:50px; font-size: 20px;">
                        <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                    </div>
                    <h6 class="mb-0">Halo, <?php echo $_SESSION['username']; ?></h6>
                    <small class="text-muted d-block mb-3">#PersonalizedFeed</small>
                    <hr>
                    <div class="list-group list-group-flush text-start">
                        <?php 
                        // Filter Referensi: Hanya News (Bukan Question)
                        $ref_q = mysqli_query($koneksi, "SELECT * FROM posts WHERE status='published' AND type != 'question' $syarat_tag ORDER BY RAND() LIMIT 3");
                        if($ref_q):
                            while($ref = mysqli_fetch_assoc($ref_q)): ?>
                                <a href="detail_berita.php?id=<?php echo $ref['id_posts']; ?>" class="list-group-item list-group-item-action px-0 border-0 py-2 small fw-bold text-truncate">
                                    <i class="bi bi-arrow-right-short text-warning"></i> <?php echo $ref['title']; ?>
                                </a>
                            <?php endwhile; 
                        endif;?>
                    </div>
                <?php else: ?>
                    <p class="small text-muted">Masuk untuk melihat referensi personal.</p>
                    <a href="login.php" class="btn btn-outline-warning btn-sm w-100 rounded-pill fw-bold">Masuk</a>
                <?php endif; ?>
            </div>
            
            <div class="p-4 rounded-4 shadow-sm text-center" style="background-color: #212529; color: white;">
                <h5 class="fw-bold text-warning mb-2">Gabung Komunitas</h5>
                <p class="small text-white-50">Ada yang ingin ditanyakan? Cari tahu di forum diskusi!</p>
                <a href="forum.php" class="btn btn-warning btn-sm w-100 fw-bold py-2 shadow-sm">DISKUSI SEKARANG</a>
            </div>
        </div>
    </div> 
</div>
<div class="modal fade" id="modalTambahPostingan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4" style="background-color: #1c1e21; color: white;">
            
            <div class="modal-header border-0 px-4 pt-4 text-center d-block">
                <h5 class="modal-title fw-bold text-warning d-inline-block"><i class="bi bi-pencil-square me-2"></i>Tulis Berita Baru</h5>
                <button type="button" class="btn-close btn-close-white float-end" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="proses_tambah_berita.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="type" value="news">
                
                <div class="modal-body px-4 py-3">
                    <div class="mb-4">
                        <input type="text" name="title" class="form-control bg-transparent border-0 border-bottom border-secondary rounded-0 fs-4 shadow-none px-0 text-white" placeholder='Apa yang sedang terjadi?' required>
                    </div>
                    
                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="small text-white-50 mb-1">Gambar Berita</label>
                            <input type="file" name="gambar" class="form-control bg-dark border-secondary btn-sm shadow-none text-white" accept="image/*" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small text-white-50 mb-1">Tags Terpilih (Pisahkan koma)</label>
                            <input type="text" name="tags" id="input-tags-final" class="form-control bg-dark border-secondary btn-sm shadow-none text-white" placeholder="Pilih di bawah atau ketik baru..." required>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3" style="background: #242526; border: 1px solid #3e4042;">
                        <label class="small fw-bold text-warning mb-2 d-block text-uppercase">Pilih Tag Populer :</label>
                        <div class="d-flex flex-wrap gap-2" id="tag-cloud">
                            <?php 
                            $q_tags = mysqli_query($koneksi, "SELECT name FROM tags ORDER BY name ASC LIMIT 15");
                            while($t = mysqli_fetch_assoc($q_tags)): ?>
                                <span class="badge border border-secondary text-white-50 fw-normal tag-item" 
                                      style="cursor:pointer; padding: 8px 12px;" 
                                      onclick="addTag('<?= $t['name'] ?>')">
                                    #<?= $t['name'] ?>
                                </span>
                            <?php endwhile; ?>
                            
                            <div class="input-group input-group-sm mt-2" style="max-width: 200px;">
                                <input type="text" id="new-tag-input" class="form-control bg-transparent border-secondary text-white" placeholder="Tag baru...">
                                <button class="btn btn-outline-warning" type="button" onclick="addNewTag()">+</button>
                            </div>
                        </div>
                    </div>

                    <textarea name="content" class="form-control bg-transparent border-0 shadow-none px-0 text-white" rows="6" placeholder="Ceritakan detail beritanya di sini..." required></textarea>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="submit" name="btn_simpan" class="btn btn-warning rounded-pill px-5 fw-bold text-dark">Posting Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fungsi buat nambahin tag dari List Database ke Inputan
function addTag(tagName) {
    let input = document.getElementById('input-tags-final');
    let currentVal = input.value.trim();
    
    if (currentVal === "") {
        input.value = tagName;
    } else {
        // Cek biar nggak duplikat
        let tagsArray = currentVal.split(',').map(s => s.trim());
        if (!tagsArray.includes(tagName)) {
            input.value = currentVal + ", " + tagName;
        }
    }
}

// Fungsi buat nambahin tag custom/baru buatan user
function addNewTag() {
    let newTagInput = document.getElementById('new-tag-input');
    let val = newTagInput.value.trim().toUpperCase();
    
    if (val !== "") {
        addTag(val); // Masukin ke input utama
        
        // Tambahin visualnya di cloud biar user tau udah nambah
        let cloud = document.getElementById('tag-cloud');
        let newBadge = document.createElement('span');
        newBadge.className = "badge border border-warning text-warning fw-normal tag-item";
        newBadge.style = "cursor:pointer; padding: 8px 12px;";
        newBadge.innerHTML = "#" + val;
        newBadge.onclick = function() { addTag(val); };
        
        cloud.insertBefore(newBadge, cloud.lastElementChild);
        newTagInput.value = "";
    }
}
</script>
<?php include 'includes/footer.php'; ?>