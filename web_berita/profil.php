<?php 
include 'includes/header.php'; 
include_once 'includes/db_config.php'; 

// Pastikan user sudah login, kalau belum tendang ke login
if(!isset($_SESSION['id_users'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit();
}

$id_user = $_SESSION['id_users'];

// 1. Ambil data utama user
$q_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id_user'");
$user = mysqli_fetch_assoc($q_user);

// 2. Ambil data minat (interests) user
$q_minat = mysqli_query($koneksi, "SELECT tag_name FROM user_interests WHERE id_users = '$id_user'");
$minat_user = [];
if($q_minat) {
    while($row = mysqli_fetch_assoc($q_minat)) {
        $minat_user[] = $row['tag_name'];
    }
}

// 3. Ambil riwayat postingan user (Berita & Forum)
$q_posts = mysqli_query($koneksi, "SELECT * FROM posts WHERE id_users = '$id_user' ORDER BY created_at DESC LIMIT 5");
?>

<style>
    body { background-color: #f4f6f9; color: #212529; }
    .profile-header {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        height: 150px;
        border-radius: 15px 15px 0 0;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border: 5px solid #fff;
        font-size: 3rem;
        margin-top: -60px;
        background-color: #ffc107;
        color: #212529;
    }
    .card-profile { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .list-group-item { border-color: #f8f9fa; }
</style>

<div class="container mt-5 mb-5" style="min-height: 70vh;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card card-profile mb-4">
                <div class="profile-header"></div>
                <div class="card-body text-center pb-5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto profile-avatar shadow-sm">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <h3 class="fw-bold mt-3 mb-1"><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-muted mb-2"><?= isset($user['email']) ? htmlspecialchars($user['email']) : 'Tidak ada email' ?></p>
                    
                    <div class="mb-3">
                        <?php 
                        $role = isset($user['role']) ? $user['role'] : 'user';
                        if($role == 'admin') {
                            echo '<span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i>Administrator</span>';
                        } else {
                            echo '<span class="badge bg-secondary px-3 py-2 rounded-pill"><i class="bi bi-person-fill me-1"></i>Member</span>';
                        }
                        ?>
                    </div>

                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <?php if($role == 'admin'): ?>
                            <a href="admin/dashboard.php" class="btn btn-dark fw-bold rounded-pill px-4"><i class="bi bi-speedometer2 me-1"></i> Dashboard Admin</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-outline-danger fw-bold rounded-pill px-4" onclick="return confirm('Yakin ingin keluar?')"><i class="bi bi-box-arrow-right me-1"></i> Keluar</a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-profile h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="bi bi-tags-fill text-warning me-2"></i>Minat Saya</h5>
                            <?php if(!empty($minat_user)): ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach($minat_user as $m): ?>
                                        <span class="badge bg-light text-dark border px-2 py-1">#<?= htmlspecialchars($m) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Belum ada minat yang dipilih.</p>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-secondary w-100 mt-4 rounded-pill fw-bold">Edit Minat</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card card-profile h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="bi bi-activity text-success me-2"></i>Aktivitas Terakhir</h5>
                            
                            <div class="list-group list-group-flush">
                                <?php if(mysqli_num_rows($q_posts) > 0): ?>
                                    <?php while($p = mysqli_fetch_assoc($q_posts)): ?>
                                        <a href="<?= $p['type'] == 'question' ? 'detail_forum.php?id='.$p['id_posts'] : 'detail_berita.php?id='.$p['id_posts'] ?>" class="list-group-item list-group-item-action px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if($p['type'] == 'question'): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill" style="font-size: 0.7rem;">Forum</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill" style="font-size: 0.7rem;">Berita</span>
                                                    <?php endif; ?>
                                                    <small class="text-muted" style="font-size: 0.8rem;"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></small>
                                                </div>
                                                <span class="badge <?= $p['status'] == 'published' ? 'bg-success' : 'bg-warning text-dark' ?> rounded-pill" style="font-size: 0.7rem;"><?= strtoupper($p['status']) ?></span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($p['title']) ?></h6>
                                        </a>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                                        Belum ada aktivitas postingan.
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>