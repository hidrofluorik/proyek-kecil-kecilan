<?php 
// Naik satu folder (../) karena file ini sekarang ada di dalam folder admin/
include '../includes/header.php'; 
include_once '../includes/db_config.php'; 
include 'auth_admin.php'; // Proteksi halaman admin

// 1. Fitur Eksekusi Hapus User
if(isset($_GET['hapus_id'])) {
    $id_hapus = intval($_GET['hapus_id']);
    
    // Hapus data dari database
    $delete = mysqli_query($koneksi, "DELETE FROM users WHERE id = $id_hapus");
    
    if($delete) {
        echo "<script>alert('Data user berhasil dihapus!'); window.location='manage_users.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user. Cek relasi database!');</script>";
    }
}

// 2. Fitur Eksekusi Ubah Role (Promote / Demote)
if(isset($_GET['ubah_role_id']) && isset($_GET['role_baru'])) {
    $id_ubah = intval($_GET['ubah_role_id']);
    $role_baru = mysqli_real_escape_string($koneksi, $_GET['role_baru']);
    
    $update = mysqli_query($koneksi, "UPDATE users SET role = '$role_baru' WHERE id = $id_ubah");
    
    if($update) {
        echo "<script>alert('Role akun berhasil diupdate!'); window.location='manage_users.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah role!');</script>";
    }
}

// Ambil semua data user dari terbaru
$q_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
?>

<style>
    body { background-color: #f4f6f9; color: #212529; }
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
</style>

<div class="container mt-5 mb-5" style="min-height: 70vh;">
    <div class="mb-3">
        <a href="dashboard.php" class="text-decoration-none text-muted fw-bold small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill me-2 text-warning"></i>Manajemen User</h3>
            <p class="text-muted small mt-1">Daftar semua pengguna dan atur hak akses Verifikator/Admin.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="ps-4 py-3">#ID</th>
                            <th scope="col" class="py-3">Username</th>
                            <th scope="col" class="py-3">Email</th>
                            <th scope="col" class="py-3">Role</th>
                            <th scope="col" class="text-center py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($q_users && mysqli_num_rows($q_users) > 0): ?>
                            <?php while($u = mysqli_fetch_assoc($q_users)): ?>
                            <tr>
                                <td class="fw-bold ps-4 text-muted"><?= $u['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 py-1">
                                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px; font-size: 0.9rem;">
                                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-bold"><?= htmlspecialchars($u['username']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?= isset($u['email']) ? htmlspecialchars($u['email']) : '<i class="text-muted small">Tidak ada data</i>' ?>
                                </td>
                                <td>
                                    <?php 
                                    $role = isset($u['role']) ? $u['role'] : 'user';
                                    if($role == 'admin') {
                                        echo '<span class="badge bg-danger"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary"><i class="bi bi-person-fill me-1"></i>User</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <?php if($role == 'admin'): ?>
                                            <a href="manage_users.php?ubah_role_id=<?= $u['id'] ?>&role_baru=user" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3" onclick="return confirm('Yakin mau copot jabatan <?= $u['username'] ?> jadi user biasa?')">
                                                <i class="bi bi-person-down me-1"></i>Demote
                                            </a>
                                        <?php else: ?>
                                            <a href="manage_users.php?ubah_role_id=<?= $u['id'] ?>&role_baru=admin" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3" onclick="return confirm('Yakin mau angkat <?= $u['username'] ?> jadi Admin/Verifikator?')">
                                                <i class="bi bi-person-up me-1"></i>Promote
                                            </a>
                                        <?php endif; ?>

                                        <a href="manage_users.php?hapus_id=<?= $u['id'] ?>" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" onclick="return confirm('Yakin mau hapus akun <?= $u['username'] ?> selamanya?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    Belum ada data user.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>