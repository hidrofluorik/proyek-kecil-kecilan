<?php 
include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

// =========================================================================
// 1. QUERY UTAMA: Ambil Pertanyaan, Preview Jawaban (comment_text), & Vote
// =========================================================================
$query_sql = "SELECT p.*, u.username, 
              (SELECT c.comment_text FROM comments c WHERE c.id_posts = p.id_posts ORDER BY c.created_at DESC LIMIT 1) as top_answer,
              (SELECT u2.username FROM comments c JOIN users u2 ON c.id_users = u2.id WHERE c.id_posts = p.id_posts ORDER BY c.created_at DESC LIMIT 1) as answerer_name,
              (SELECT COUNT(*) FROM comments WHERE id_posts = p.id_posts) as total_answers,
              (SELECT IFNULL(SUM(vote_type), 0) FROM votes v WHERE v.id_posts = p.id_posts) as net_votes
              FROM posts p 
              JOIN users u ON p.id_users = u.id 
              WHERE p.type = 'question' AND p.status = 'published'";

// 2. LOGIKA FILTER TAGS
if(isset($_GET['tag'])) {
    $tag = mysqli_real_escape_string($koneksi, $_GET['tag']);
    $query_sql .= " AND p.tags LIKE '%$tag%'";
}

$query_sql .= " ORDER BY p.created_at DESC";
$query_forum = mysqli_query($koneksi, $query_sql);
?>

<style>
    body { background-color: #f1f2f2; color: #282829; }
    
    /* FIX LAYERING */
    .container { position: relative; z-index: 1; }

    .sidebar-link { 
        padding: 10px 15px; border-radius: 8px; color: #636466; 
        text-decoration: none; display: block; font-size: 0.95rem;
        position: relative; z-index: 50 !important;
    }
    .sidebar-link.active { background: #fee7e7; color: #b92b27; font-weight: bold; }

    .quora-card {
        background: #fff; border: 1px solid #dee2e6; border-radius: 8px;
        padding: 16px; position: relative; z-index: 10;
    }

    .question-title, .quora-btn, .btn-vote, .action-link, button[data-bs-target] {
        position: relative; z-index: 100 !important; cursor: pointer !important;
        pointer-events: auto !important;
    }

    .question-title { 
        font-size: 1.2rem; font-weight: 800; color: #282829; 
        text-decoration: none; display: block; margin-top: 5px;
    }
    .question-title:hover { text-decoration: underline; color: #2e69ff; }
    
    /* FIX: RINGKASAN JAWABAN (3-4 BARIS) */
    .answer-preview {
        font-size: 0.95rem; color: #333; line-height: 1.5;
        display: -webkit-box; 
        -webkit-line-clamp: 3; 
        -webkit-box-orient: vertical; 
        overflow: hidden; 
        margin-top: 5px;
    }

    .vote-container {
        background-color: #f7f7f8; border-radius: 20px;
        display: inline-flex; align-items: center; border: 1px solid #dee2e6;
        position: relative; z-index: 101;
    }
    .btn-vote { border: none; background: none; padding: 6px 15px; color: #636466; }
    .btn-upvote:hover { color: #2e69ff; background: #ebf0ff; border-radius: 20px 0 0 20px; }
    .btn-downvote:hover { color: #cb4023; background: #fdf0ed; border-radius: 0 20px 20px 0; }
    
    .quora-btn {
        background: none; border: none; color: #636466;
        font-size: 0.9rem; font-weight: 500; padding: 8px;
        text-decoration: none;
    }
    .quora-btn:hover { background: #f7f7f8; border-radius: 8px; }
    .sticky-sidebar { position: sticky; top: 85px; z-index: 1000; }

    /* DARK MODAL STYLING (MATCHING IMAGE) */
    .modal-dark .modal-content {
        background-color: #1c1e21;
        color: #fff;
        border-radius: 15px;
        border: 1px solid #3e4042;
    }
    .modal-dark .form-control {
        background-color: transparent; border: none;
        border-bottom: 1px solid #3e4042; color: #fff;
        border-radius: 0; padding-left: 0;
    }
    .modal-dark .tag-box { background: #242526; border: 1px solid #3e4042; padding: 15px; border-radius: 10px; }
    .modal-dark .badge-tag { cursor: pointer; border: 1px solid #3e4042; background: transparent; color: #b0b3b8; padding: 8px 12px; }
</style>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-2 d-none d-lg-block">
            <div class="sticky-sidebar">
                <p class="small fw-bold text-muted mb-2 px-2 text-uppercase">Kategori</p>
                <a href="forum.php" class="sidebar-link <?= !isset($_GET['tag']) ? 'active' : '' ?> mb-1">
                    <i class="bi bi-grid-fill me-2"></i>Semua Forum
                </a>
                <?php 
                $res_tags = mysqli_query($koneksi, "SELECT name FROM tags LIMIT 12");
                while($t = mysqli_fetch_assoc($res_tags)): 
                    $active_tag = (isset($_GET['tag']) && $_GET['tag'] == $t['name']) ? 'active' : '';
                ?>
                    <a href="forum.php?tag=<?= $t['name'] ?>" class="sidebar-link <?= $active_tag ?> mb-1 text-truncate">
                        <i class="bi bi-hash me-1"></i><?= $t['name'] ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-lg-7 col-md-12">
            <div class="quora-card shadow-sm mb-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width:38px; height:38px;">
                        <?= isset($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 1)) : '?' ?>
                    </div>
                    <button class="form-control rounded-pill bg-light text-start text-muted border py-2 px-3 shadow-none quora-btn" data-bs-toggle="modal" data-bs-target="#modalAjukanPertanyaan">
                        Apa yang ingin Anda tanyakan atau bagikan?
                    </button>
                </div>
                <div class="d-flex justify-content-around border-top pt-2">
                    <button class="quora-btn w-100" data-bs-toggle="modal" data-bs-target="#modalAjukanPertanyaan"><i class="bi bi-question-circle me-1 text-primary"></i>Tanya</button>
                    <a href="#forum-feed" class="quora-btn w-100 text-center text-decoration-none border-start border-end rounded-0"><i class="bi bi-pencil-square me-1 text-success"></i>Jawab</a>
                    <button class="quora-btn w-100" data-bs-toggle="modal" data-bs-target="#modalAjukanPertanyaan"><i class="bi bi-megaphone me-1 text-danger"></i>Post</button>
                </div>
            </div>

            <div id="forum-feed">
                <?php if(mysqli_num_rows($query_forum) > 0): ?>
                    <?php while($q = mysqli_fetch_assoc($query_forum)): ?>
                    <div class="quora-card shadow-sm mb-3">
                        <div class="user-info mb-2 small text-muted">
                            <strong><?= $q['username'] ?></strong> <span class="mx-2">•</span> <span><?= date('d M', strtotime($q['created_at'])) ?></span>
                        </div>
                        
                        <a href="detail_forum.php?id=<?= $q['id_posts'] ?>" class="question-title"><?= $q['title'] ?></a>

                        <?php if($q['top_answer']): ?>
                            <div class="mt-3 p-3 rounded bg-light bg-opacity-75 border-start border-4 border-warning">
                                <small class="text-muted d-block mb-1">Jawaban terbaru oleh <strong><?= $q['answerer_name'] ?></strong>:</small>
                                <div class="answer-preview"><?= strip_tags($q['top_answer']) ?></div>
                                <a href="detail_forum.php?id=<?= $q['id_posts'] ?>" class="text-primary small text-decoration-none fw-bold mt-2 d-inline-block">Baca selengkapnya...</a>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex align-items-center gap-3 mt-3 pt-2 border-top">
                            <div class="vote-container">
                                <button class="btn-vote btn-upvote" title="Upvote" onclick="kirimVote(<?= $q['id_posts'] ?>, 1)">
                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                </button>
                                
                                <span class="vote-count" id="v-count-<?= $q['id_posts'] ?>"><?= $q['net_votes'] ?></span>
                                
                                <button class="btn-vote btn-downvote border-start" title="Downvote" onclick="kirimVote(<?= $q['id_posts'] ?>, -1)">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </button>
                            </div>

                            <a href="detail_forum.php?id=<?= $q['id_posts'] ?>" class="action-link text-decoration-none">
                                <i class="bi bi-chat-text me-1"></i> <?= $q['total_answers'] ?>
                            </a>
                            <button class="action-link border-0 bg-transparent ms-auto" onclick="navigator.clipboard.writeText(window.location.origin + '/detail_forum.php?id=<?= $q['id_posts'] ?>'); alert('Link disalin!')"><i class="bi bi-share"></i></button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-patch-question fs-1 text-muted opacity-50 mb-3 d-block"></i>
                        <h5 class="text-muted">Belum ada diskusi yang dimulai.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-3 d-none d-lg-block">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm rounded-3 mb-3 p-3 bg-white">
                    <h6 class="fw-bold mb-2 border-bottom pb-2">Aturan Forum</h6>
                    <ul class="small text-muted ps-3 mb-0">
                        <li class="mb-2">Sopan dalam berdiskusi.</li>
                        <li>Jangan spam atau SARA.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-dark" id="modalAjukanPertanyaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-warning mx-auto"><i class="bi bi-pencil-square me-2"></i>Ajukan Pertanyaan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_tambah_berita.php" method="POST">
                <input type="hidden" name="type" value="question">
                <div class="modal-body px-4">
                    <input type="text" name="title" class="form-control fs-4 mb-4" placeholder="Apa yang ingin lo tanyain?" required>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="small text-white-50 mb-1">Gambar Pendukung (Opsional)</label>
                            <input type="file" name="gambar" class="form-control bg-dark border-secondary btn-sm shadow-none text-white" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="small text-white-50 mb-1">Tags Terpilih</label>
                            <input type="text" name="tags" id="forum-tags-input" class="form-control" placeholder="Tag...">
                        </div>
                    </div>
                    <div class="tag-box mb-4">
                        <label class="small fw-bold text-warning mb-2 d-block text-uppercase">Tag Populer :</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $q_pop_tags = mysqli_query($koneksi, "SELECT name FROM tags LIMIT 8");
                            while($pt = mysqli_fetch_assoc($q_pop_tags)): ?>
                                <span class="badge badge-tag" onclick="addTagForum('<?= $pt['name'] ?>')">#<?= $pt['name'] ?></span>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <textarea name="content" class="form-control border-0 bg-transparent" rows="4" placeholder="Detail pertanyaan lo..." required></textarea>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="submit" name="btn_simpan" class="btn btn-warning w-100 fw-bold py-2 rounded-pill text-dark">Posting Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function kirimVote(postId, type) {
    <?php if(!isset($_SESSION['id_users'])): ?>
        alert('Silakan login untuk memberikan vote!');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>

    const formData = new URLSearchParams();
    formData.append('id_posts', postId);
    formData.append('vote_type', type);

    fetch('proses_vote_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`v-count-${postId}`).innerText = data.new_count;
        } else {
            alert('Gagal memberikan vote. Silakan coba lagi.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function addTagForum(tagName) {
    let input = document.getElementById('forum-tags-input');
    let currentVal = input.value.trim();
    if (currentVal === "") { input.value = tagName; } 
    else {
        let tagsArray = currentVal.split(',').map(s => s.trim());
        if (!tagsArray.includes(tagName)) { input.value = currentVal + ", " + tagName; }
    }
}
</script>

<?php include 'includes/footer.php'; ?>