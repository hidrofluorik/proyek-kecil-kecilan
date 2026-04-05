<?php 
include '../includes/header.php'; 
include 'auth_admin.php'; 
?>

<div class="container mt-5" style="min-height: 80vh;">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-white p-5 rounded-4 shadow-sm border">
                <h1 class="fw-bold">Selamat Datang, Admin <?php echo $_SESSION['username']; ?>!</h1>
                <p class="text-muted">Di sini kamu bisa mengatur berita hot, mengelola user, dan verifikator.</p>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white border-0 shadow-sm mb-3 rounded-3 h-100">
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-megaphone-fill mb-3" style="font-size: 2.5rem;"></i>
                                <h3 class="fw-bold">Kelola Berita</h3>
                                <p class="small">Atur Headline, Carousel, dan Hot News</p>
                                <a href="manage_news.php" class="btn btn-light btn-sm fw-bold">Buka Menu</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white border-0 shadow-sm mb-3 rounded-3 h-100">
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                                <i class="bi bi-people-fill mb-3" style="font-size: 2.5rem;"></i>
                                <h3 class="fw-bold">Data User</h3>
                                <p class="small">Lihat Member & Promosikan Verifikator</p>
                                <a href="manage_users.php" class="btn btn-light btn-sm fw-bold">Buka Menu</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>