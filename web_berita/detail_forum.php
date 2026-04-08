<?php 
include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: forum.php");
    exit();
}

$id_posts = intval($_GET['id']);

$query = mysqli_query($koneksi, "SELECT p.*, u.username 
                                 FROM posts p 
                                 JOIN users u ON p.id_users = u.id 
                                 WHERE p.id_posts = $id_posts AND p.type = 'question' AND p.status = 'published'");

$data = mysqli_fetch_assoc($query);

if(!$data){
    echo "<div class='container mt-5 py-5 text-center'>
            <h1 class='display-1 fw-bold text-secondary'>404</h1>
            <h3 class='text-dark'>Diskusi tidak ditemukan.</h3>
            <a href='forum.php' class='btn btn-warning mt-3 fw-bold rounded-pill px-4'>Kembali ke Forum</a>
          </div>";
    include 'includes/footer.php';
    exit();
}
?>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<style>
    body { background-color: #f8f9fa; color: #212529; }
    .q-card { border-left: 5px solid #ffc107; background-color: #fff; }
    .ans-card { background-color: #fff; border: 1px solid #dee2e6; }
    
    /* Perbaikan tampilan gambar di dalam jawaban agar tidak melebihi batas card */
    .jawaban-konten img { max-width: 100% !important; height: auto !important; border-radius: 8px; margin: 10px 0; }
</style>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="mb-4">
                <a href="forum.php" class="text-decoration-none text-muted fw-bold small">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Forum
                </a>
            </div>

            <div class="card q-card shadow-sm p-4 p-md-5 mb-5 rounded-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px;">
                        <?php echo strtoupper(substr($data['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold text-dark"><?php echo $data['username']; ?> <span class="text-muted fw-normal small">bertanya:</span></p>
                        <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($data['created_at'])); ?></small>
                    </div>
                </div>

                <h2 class="fw-bold text-dark mb-3"><?php echo $data['title']; ?></h2>
                <div class="text-dark mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($data['content'])); ?>
                </div>
            </div>

            <div id="kolom-jawaban">
                <h5 class="fw-bold mb-4 text-dark">Jawaban Komunitas</h5>

                <?php if(isset($_SESSION['id_users'])): ?>
                    <div class="card ans-card shadow-sm p-4 mb-5 rounded-4">
                        <form action="proses_komentar.php" method="POST">
                            <input type="hidden" name="id_posts" value="<?php echo $id_posts; ?>">
                            <input type="hidden" name="parent_id" value="0">
                            <input type="hidden" name="asal_halaman" value="forum">
                            
                            <div class="mb-3">
                                <label class="fw-bold text-dark mb-2">Tulis Jawaban Anda:</label>
                                <textarea name="comment_text" id="summernote_editor" class="form-control bg-white" required></textarea>
                            </div>
                            <button type="submit" name="kirim_komentar" class="btn btn-dark fw-bold px-4 rounded-pill shadow-sm">Kirim Jawaban</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border text-center mb-5 rounded-3 text-dark">
                        Silakan <a href="login.php" class="fw-bold text-warning text-decoration-none">Masuk</a> untuk memberikan jawaban.
                    </div>
                <?php endif; ?>

                <div class="jawaban-list">
                    <?php 
                    $q_komen = mysqli_query($koneksi, "SELECT c.*, u.username FROM comments c JOIN users u ON c.id_users = u.id WHERE c.id_posts = '$id_posts' AND c.parent_id = 0 ORDER BY c.created_at DESC");
                    
                    if($q_komen && mysqli_num_rows($q_komen) > 0):
                        while($komen = mysqli_fetch_assoc($q_komen)):
                    ?>
                        <div class="card ans-card shadow-sm p-4 mb-4 rounded-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px; font-size: 0.9rem;">
                                        <?php echo strtoupper(substr($komen['username'], 0, 1)); ?>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($komen['username']); ?></h6>
                                </div>
                                <small class="text-muted" style="font-size: 0.8rem;"><?php echo date('d M Y, H:i', strtotime($komen['created_at'])); ?></small>
                            </div>
                            
                            <div class="mb-3 text-dark jawaban-konten">
                                <?php echo $komen['comment_text']; ?>
                            </div>
                            
                            <?php if(isset($_SESSION['id_users'])): ?>
                                <button class="btn btn-sm btn-light border fw-bold text-secondary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm<?php echo $komen['id_comment']; ?>">
                                    <i class="bi bi-chat-dots me-1"></i> Tanggapi
                                </button>

                                <div class="collapse mt-3" id="replyForm<?php echo $komen['id_comment']; ?>">
                                    <form action="proses_komentar.php" method="POST" class="d-flex gap-2 p-3 bg-light rounded-3 border">
                                        <input type="hidden" name="id_posts" value="<?php echo $id_posts; ?>">
                                        <input type="hidden" name="parent_id" value="<?php echo $komen['id_comment']; ?>">
                                        <input type="hidden" name="asal_halaman" value="forum">
                                        <input type="text" name="comment_text" class="form-control border-0 shadow-none" placeholder="Tulis tanggapan singkat..." required>
                                        <button type="submit" name="kirim_komentar" class="btn btn-secondary btn-sm fw-bold px-3">Kirim</button>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <?php 
                            $id_parent = $komen['id_comment'];
                            $q_reply = mysqli_query($koneksi, "SELECT c.*, u.username FROM comments c JOIN users u ON c.id_users = u.id WHERE c.id_posts = '$id_posts' AND c.parent_id = '$id_parent' ORDER BY c.created_at ASC");
                            
                            if($q_reply && mysqli_num_rows($q_reply) > 0):
                                echo '<div class="mt-4 ps-4 border-start border-3 border-secondary bg-light p-3 rounded-end-4">'; 
                                while($reply = mysqli_fetch_assoc($q_reply)):
                            ?>
                                    <div class="mb-3 last-child-mb-0">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <strong class="small text-dark"><?php echo htmlspecialchars($reply['username']); ?></strong>
                                            <small class="text-muted" style="font-size: 0.75rem;">• <?php echo date('d M Y, H:i', strtotime($reply['created_at'])); ?></small>
                                        </div>
                                        <p class="mb-0 text-dark small"><?php echo $reply['comment_text']; ?></p>
                                    </div>
                            <?php 
                                endwhile;
                                echo '</div>'; 
                            endif; 
                            ?>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo '<div class="text-center py-5 text-muted"><i class="bi bi-chat-square-text fs-1 d-block mb-3 opacity-50"></i>Belum ada jawaban. Bantu jawab pertanyaan ini!</div>';
                    endif; 
                    ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote_editor').summernote({
            placeholder: 'Tulis jawaban lengkap Anda di sini. Anda bisa insert gambar, link, atau memformat teks...',
            tabsize: 2,
            height: 250,
            dialogsInBody: true, // <--- INI KUNCI BIAR GAMBAR GAK LARI KE BAWAH
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']], 
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
<?php include 'includes/footer.php'; ?>