<?php include 'includes/header.php'; ?>

<section id="hot-news" class="mb-5">
    <div id="carouselHotNews" class="carousel slide" data-bs-ride="carousel">
        
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselHotNews" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselHotNews" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselHotNews" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            
            <div class="carousel-item active" data-bs-interval="5000">
                <img src="assets/gambar/gambar1.jpg" alt="Berita Utama 1">
                <div class="carousel-caption">
                    <span class="badge bg-warning text-dark mb-2">HOT NEWS</span>
                    <h5>Dunia IoT Berkembang Pesat di Indonesia</h5>
                    <p>Implementasi teknologi smart city mulai merambah kota-kota besar untuk efisiensi energi dan transportasi.</p>
                    <a href="#" class="btn btn-warning btn-sm fw-bold">BACA SELENGKAPNYA</a>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="5000">
                <img src="assets/gambar/gambar2.jpg" alt="Berita Utama 2">
                <div class="carousel-caption">
                    <span class="badge bg-danger mb-2">UPDATE TERKINI</span>
                    <h5>Waspada Modus Penipuan Online Terbaru 2026</h5>
                    <p>Kenali ciri-ciri link phising yang sering mengatasnamakan instansi resmi pemerintah atau bank nasional.</p>
                    <a href="#" class="btn btn-danger btn-sm fw-bold">BACA SELENGKAPNYA</a>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="5000">
                <img src="assets/gambar/gambar3.jpg" alt="Berita Utama 3">
                <div class="carousel-caption">
                    <span class="badge bg-primary mb-2">TEKNOLOGI</span>
                    <h5>Review Laptop Gaming Budget Terbaik</h5>
                    <p>Apakah laptop kelas entri masih sanggup menjalankan game AAA dengan settingan tinggi di tahun ini?</p>
                    <a href="#" class="btn btn-primary btn-sm fw-bold">BACA SELENGKAPNYA</a>
                </div>
            </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselHotNews" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselHotNews" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</section>

<div class="container">
    <div class="row">
        
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h3 class="fw-bold mb-0">Berita Terbaru</h3>
                <a href="#" class="text-decoration-none text-muted small">Lihat Semua →</a>
            </div>
            
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="assets/gambar/gambar1.jpg" class="img-fluid h-100" style="object-fit: cover;" alt="Thumbnail">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Ini Adalah Judul Berita yang Akan Diambil Dari Database</h5>
                            <p class="card-text text-muted small">Ringkasan isi berita ditaruh di sini untuk memberikan gambaran singkat kepada pembaca mengenai topik yang dibahas...</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-light text-dark border">#Nasional</span>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Validitas Masyarakat:</small>
                                    <span class="badge bg-success">85% Valid</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        <div class="col-md-4">
            <h4 class="fw-bold mb-4">Referensi Untukmu</h4>
            
            <div class="card shadow-sm border-0 bg-white p-3 mb-4">
                <?php if(isset($_SESSION['username'])): ?>
                    <p class="small text-muted mb-3">Menampilkan berita berdasarkan minat Anda: <b>#Teknologi</b></p>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action px-0 border-0">
                            <h6 class="fw-bold mb-1 small">Masa Depan AI di Sektor Pendidikan Tinggi</h6>
                            <small class="text-muted">30 Menit yang lalu</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action px-0 border-0">
                            <h6 class="fw-bold mb-1 small">Tips Memilih Komponen PC untuk Rendering 3D</h6>
                            <small class="text-muted">2 Jam yang lalu</small>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <p class="small text-muted mb-3">Login untuk mendapatkan rekomendasi berita sesuai minatmu.</p>
                        <a href="login.php" class="btn btn-outline-warning btn-sm px-4">Masuk Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-warning p-4 rounded-3 text-center shadow-sm">
                <h5 class="fw-bold">Gabung Komunitas</h5>
                <p class="small mb-0">Diskusikan berita dan berikan verifikasi Anda!</p>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>