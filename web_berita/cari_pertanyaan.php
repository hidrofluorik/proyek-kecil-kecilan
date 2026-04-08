<?php 
include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

// Logika Search & Tag
$search = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';
$tag_filter = isset($_GET['tag']) ? mysqli_real_escape_string($koneksi, $_GET['tag']) : '';

$query = "SELECT p.*, u.username, 
          (SELECT COUNT(*) FROM comments WHERE id_posts = p.id_posts) as total_answers
          FROM posts p 
          JOIN users u ON p.id_users = u.id 
          WHERE p.type = 'question' AND p.status = 'published'";

if($search != '') { $query .= " AND (p.title LIKE '%$search%' OR p.content LIKE '%$search%')"; }
if($tag_filter != '') { $query .= " AND p.tags LIKE '%$tag_filter%'"; }

$query .= " ORDER BY p.created_at DESC";
$res = mysqli_query($koneksi, $query);
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4">Cari Pertanyaan untuk Dijawab</h3>
            
            <form action="" method="GET" class="mb-4">
                <div class="input-group mb-3 shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari topik atau kata kunci..." value="<?= $search ?>">
                    <button class="btn btn-warning fw-bold px-4" type="submit">Cari</button>
                </div>
                
                <div class="d-flex flex-wrap gap-2">
                    <small class="text-muted w-100 mb-1">Filter Tag Populer:</small>
                    <?php 
                    $tags = mysqli_query($koneksi, "SELECT name FROM tags LIMIT 8");
                    while($t = mysqli_fetch_assoc($tags)): ?>
                        <a href="?tag=<?= $t['name'] ?>" class="badge rounded-pill border text-decoration-none <?= ($tag_filter == $t['name']) ? 'bg-dark text-white' : 'bg-light text-dark' ?> py-2 px-3">
                            #<?= $t['name'] ?>
                        </a>
                    <?php endwhile; ?>
                    <?php if($tag_filter != ''): ?> <a href="cari_pertanyaan.php" class="text-danger small ms-2">Reset Filter</a> <?php endif; ?>
                </div>
            </form>

            <?php if(mysqli_num_rows($res) > 0): while($p = mysqli_fetch_assoc($res)): ?>
                <div class="card border-0 shadow-sm mb-3 rounded-4 p-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="fw-bold mb-1"><a href="detail_forum.php?id=<?= $p['id_posts'] ?>" class="text-dark text-decoration-none"><?= $p['title'] ?></a></h5>
                            <span class="badge bg-light text-muted border"><?= $p['total_answers'] ?> Jawaban</span>
                        </div>
                        <p class="text-muted small mb-3"><?= substr(strip_tags($p['content']), 0, 150) ?>...</p>
                        <a href="detail_forum.php?id=<?= $p['id_posts'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4">Berikan Jawaban</a>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div class="text-center py-5">
                    <p class="text-muted">Tidak ada pertanyaan yang sesuai pencarian lo, Yes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>