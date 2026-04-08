<?php 
include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

// 1. Tangkap ID dari URL dan amankan
if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id_posts = intval($_GET['id']);

// 2. Ambil data berita lengkap + gabung tabel users buat ambil nama penulis
$query = mysqli_query($koneksi, "SELECT p.*, u.username 
                                 FROM posts p 
                                 JOIN users u ON p.id_users = u.id 
                                 WHERE p.id_posts = $id_posts AND p.status = 'published'");

$data = mysqli_fetch_assoc($query);

// --- LOGIKA MENGHITUNG VOTE & VALIDITAS ---
$q_up = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM votes WHERE id_posts = $id_posts AND vote_type = 1");
$upvotes = mysqli_fetch_assoc($q_up)['jml'];

$q_down = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM votes WHERE id_posts = $id_posts AND vote_type = -1");
$downvotes = mysqli_fetch_assoc($q_down)['jml'];

$total_votes = $upvotes + $downvotes;
$persen_valid = ($total_votes > 0) ? round(($upvotes / $total_votes) * 100) : 0;
$persen_hoaks = ($total_votes > 0) ? round(($downvotes / $total_votes) * 100) : 0;
// ------------------------------------------

// Jika berita tidak ada atau statusnya masih 'pending'
if(!$data){
    echo "<div class='container mt-5 py-5 text-center'>
            <h1 class='display-1 fw-bold text-secondary'>404</h1>
            <h3 class='text-dark'>Berita tidak ditemukan atau masih menunggu verifikasi Admin.</h3>
            <a href='index.php' class='btn btn-warning mt-3 fw-bold rounded-pill px-4'>Kembali ke Beranda</a>
          </div>";
    include 'includes/footer.php';
    exit();
}
?>

<style>
    /* UBAH KE TEMA TERANG (LIGHT MODE) */
    body { background-color: #f8f9fa; color: #212529; } 
    .content-card { background-color: #ffffff; border-radius: 20px; border: 1px solid #dee2e6; }
    .news-title { font-weight: 800; letter-spacing: -1px; line-height: 1.2; color: #000; }
    .news-text { font-size: 1.15rem; line-height: 1.9; color: #495057; text-align: justify; }
    .news-text b, .news-text strong { color: #212529; }
    .img-detail { width: 100%; max-height: 550px; object-fit: cover; border-radius: 15px; }
    .sidebar-widget { background-color: #ffffff; border-radius: 15px; border: 1px solid #dee2e6; }
    .badge-tag { background: rgba(255, 193, 7, 0.1); color: #d39e00; border: 1px solid rgba(255, 193, 7, 0.3); }
</style>

<main class="container mt-4 mb-5">
    <div class="row g-4">
        
        <div class="col-lg-8">
            <article class="content-card p-4 p-md-5 shadow-sm mb-4">
                
                <div class="mb-3 d-flex gap-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill"><?php echo strtoupper($data['type']); ?></span>
                    <span class="badge badge-tag px-3 py-2 rounded-pill fw-bold">#<?php echo $data['tags'] ?: 'Umum'; ?></span>
                </div>

                <h1 class="news-title mb-4"><?php echo $data['title']; ?></h1>

                <div class="d-flex align-items-center mb-4 py-3 border-top border-bottom border-light">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px; font-size: 1.2rem;">
                        <?php echo strtoupper(substr($data['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold text-dark"><?php echo $data['username']; ?></p>
                        <small class="text-muted"><?php echo date('l, d F Y | H:i', strtotime($data['created_at'])); ?> WIB</small>
                    </div>
                </div>

                <?php if($data['gambar']): ?>
                    <div class="mb-4">
                        <img src="assets/img/<?php echo $data['gambar']; ?>" class="img-detail shadow-sm" alt="Berita Image">
                    </div>
                <?php endif; ?>

                <div class="news-text">
                    <?php echo nl2br($data['content']); ?>
                </div>
<div class="mt-5 pt-4 border-top border-light">
    
    <h6 class="fw-bold mb-2 text-dark">Bar Validitas Berita</h6>
    <div class="progress mb-3 shadow-sm" style="height: 25px; border-radius: 10px;">
        <?php if($total_votes == 0): ?>
            <div class="progress-bar bg-secondary bg-opacity-25 text-dark fw-bold w-100" role="progressbar">Belum ada vote</div>
        <?php else: ?>
            <div class="progress-bar bg-success fw-bold" role="progressbar" style="width: <?php echo $persen_valid; ?>%;" aria-valuenow="<?php echo $persen_valid; ?>" aria-valuemin="0" aria-valuemax="100">
                <?php echo $persen_valid; ?>% Valid
            </div>
            <div class="progress-bar bg-danger fw-bold" role="progressbar" style="width: <?php echo $persen_hoaks; ?>%;" aria-valuenow="<?php echo $persen_hoaks; ?>" aria-valuemin="0" aria-valuemax="100">
                <?php echo $persen_hoaks; ?>% Diragukan
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2">
            <form action="proses_vote.php" method="POST">
                <input type="hidden" name="id_posts" value="<?php echo $id_posts; ?>">
                <input type="hidden" name="vote_type" value="1">
                <button type="submit" class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3">
                    <i class="bi bi-hand-thumbs-up-fill me-1"></i> Upvote (<?php echo $upvotes; ?>)
                </button>
            </form>
            
            <form action="proses_vote.php" method="POST">
                <input type="hidden" name="id_posts" value="<?php echo $id_posts; ?>">
                <input type="hidden" name="vote_type" value="-1">
                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold rounded-pill px-3">
                    <i class="bi bi-hand-thumbs-down-fill me-1"></i> Downvote (<?php echo $downvotes; ?>)
                </button>
            </form>
        </div>

        <a href="index.php" class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Beranda
        </a>
    </div>
</div>
            </article>

            <div class="card shadow-sm border-0 rounded-4 p-4 mt-4" style="background-color: #ffffff; color: #212529; border: 1px solid #dee2e6 !important;" id="kolom-komentar">
                <h4 class="fw-bold mb-4 border-bottom pb-3 text-dark">Komentar Diskusi</h4>

                <?php if(isset($_SESSION['id_users'])): ?>
                    <form action="proses_komentar.php" method="POST" class="mb-5">
                        <input type="hidden" name="id_posts" value="<?php echo $id_posts; ?>">
                        <input type="hidden" name="parent_id" value="0">
                        <div class="d-flex gap-3">
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:45px; height:45px; font-size: 1.2rem;">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1">
                                <textarea name="comment_text" class="form-control bg-light border-secondary-subtle mb-2 shadow-none" rows="3" placeholder="Tulis komentar Anda..." style="color: #212529;" required></textarea>
                                <button type="submit" name="kirim_komentar" class="btn btn-warning fw-bold px-4 shadow-sm text-dark">Kirim Komentar</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-light border text-center mb-5 rounded-3 text-dark">
                        Silakan <a href="login.php" class="fw-bold text-warning text-decoration-none">Masuk</a> untuk ikut berdiskusi.
                    </div>
                <?php endif; ?>

                <div class="komentar-list text-start">
                    <?php 
$q_komen = mysqli_query($koneksi, "SELECT c.*, u.username FROM comments c JOIN users u ON c.id_users = u.id WHERE c.id_posts = '$id_posts' AND c.parent_id = 0 ORDER BY c.created_at DESC");                    
                    if($q_komen && mysqli_num_rows($q_komen) > 0):
                        while($komen = mysqli_fetch_assoc($q_komen)):
                    ?>
                        <div class="d-flex gap-3 mb-4">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:45px; height:45px;">
                                <?php echo strtoupper(substr($komen['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="bg-light border p-3 rounded-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($komen['username']); ?></h6>
                                        <small class="text-muted" style="font-size: 0.8rem;"><?php echo date('d M Y, H:i', strtotime($komen['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-0 text-dark" style="font-size: 0.95rem;"><?php echo nl2br(htmlspecialchars($komen['comment_text'])); ?></p>
                                </div>
                                
                                <?php if(isset($_SESSION['id_users'])): ?>
                                    <div class="ms-2 mt-1">
                                        <button class="btn btn-link btn-sm text-secondary text-decoration-none p-0 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm<?php echo $komen['id_comment']; ?>">
                                            <i class="bi bi-reply-fill"></i> Balas
                                        </button>
                                    </div>

                                    <div class="collapse mt-2 ms-2" id="replyForm<?php echo $komen['id_comment']; ?>">
                                        <form action="proses_komentar.php" method="POST" class="d-flex gap-2 bg-light p-2 rounded-3 border">
                                            <input type="hidden" name="id_posts" value="<?php echo $id_posts; ?>">
                                            <input type="hidden" name="parent_id" value="<?php echo $komen['id_comment']; ?>">
                                            <input type="text" name="comment_text" class="form-control form-control-sm border-0 bg-transparent shadow-none" placeholder="Tulis balasan..." style="color: #212529;" required>
                                            <button type="submit" name="kirim_komentar" class="btn btn-dark btn-sm fw-bold px-3 rounded-pill">Kirim</button>
                                        </form>
                                    </div>
                                <?php endif; ?>

                                <?php 
                                $id_parent = $komen['id_comment'];
$q_reply = mysqli_query($koneksi, "SELECT c.*, u.username FROM comments c JOIN users u ON c.id_users = u.id WHERE c.id_posts = '$id_posts' AND c.parent_id = '$id_parent' ORDER BY c.created_at ASC");                                
                                if($q_reply && mysqli_num_rows($q_reply) > 0):
                                    echo '<div class="mt-3 ps-4 border-start border-3 border-warning">'; 
                                    while($reply = mysqli_fetch_assoc($q_reply)):
                                ?>
                                        <div class="d-flex gap-2 mb-3">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:35px; height:35px; font-size: 0.85rem;">
                                                <?php echo strtoupper(substr($reply['username'], 0, 1)); ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="bg-white border p-2 rounded-3 shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <strong class="small text-dark"><?php echo htmlspecialchars($reply['username']); ?></strong>
                                                        <small class="text-muted" style="font-size: 0.75rem;"><?php echo date('d M Y, H:i', strtotime($reply['created_at'])); ?></small>
                                                    </div>
                                                    <p class="mb-0 text-dark small"><?php echo nl2br(htmlspecialchars($reply['comment_text'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                <?php 
                                    endwhile;
                                    echo '</div>'; 
                                endif; 
                                ?>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo '<div class="text-center py-5 text-muted"><i class="bi bi-chat-dots fs-1 d-block mb-2 opacity-50"></i>Belum ada komentar. Jadilah yang pertama berpendapat!</div>';
                    endif; 
                    ?>
                </div>
            </div>
            </div>

        <div class="col-lg-4">
            <div class="sidebar-widget p-4 shadow-sm position-sticky" style="top: 100px;">
                <h5 class="fw-bold mb-4 border-start border-warning border-4 ps-3 text-dark">BACA JUGA</h5>
                
                <?php 
                $q_rec = mysqli_query($koneksi, "SELECT * FROM posts WHERE id_posts != $id_posts AND status = 'published' ORDER BY RAND() LIMIT 4");
                while($rec = mysqli_fetch_assoc($q_rec)):
                ?>
                <div class="d-flex mb-4">
                    <?php if($rec['gambar']): ?>
                        <img src="assets/img/<?php echo $rec['gambar']; ?>" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="ms-3">
                        <a href="detail_berita.php?id=<?php echo $rec['id_posts']; ?>" class="text-dark text-decoration-none fw-bold small d-block mb-1 lh-sm">
                            <?php echo substr($rec['title'], 0, 60); ?>...
                        </a>
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="bi bi-calendar3 me-1"></i> <?php echo date('d M Y', strtotime($rec['created_at'])); ?>
                        </small>
                    </div>
                </div>
                <?php endwhile; ?>

                <div class="mt-5 p-4 bg-warning rounded-4 text-dark text-center shadow-sm">
                    <h6 class="fw-bold mb-2">Ingin Berbagi Info?</h6>
                    <p class="small mb-3">Tulis berita versimu dan kumpulkan poin reputasi!</p>
                    <a href="index.php" class="btn btn-dark btn-sm rounded-pill w-100 fw-bold shadow-sm">Tulis Sekarang</a>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>