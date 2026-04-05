<?php 
include 'includes/db_config.php'; 
include 'includes/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark">DAFTAR AKUN</h2>
                        <p class="text-muted">Lengkapi data diri untuk mulai berkontribusi</p>
                    </div>

                    <?php 
                    if(isset($_POST['register'])){
                        $user   = mysqli_real_escape_string($koneksi, $_POST['username']);
                        $pass   = $_POST['password'];
                        $minat  = isset($_POST['minat']) ? $_POST['minat'] : []; 

                        // Validasi Backend: Cek panjang password lagi
                        if(strlen($pass) < 6) {
                            echo "<div class='alert alert-danger'>Password minimal harus 6 karakter!</div>";
                        } else {
                            $cek_user = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$user'");
                            if(mysqli_num_rows($cek_user) > 0){
                                echo "<div class='alert alert-danger'>Username sudah digunakan!</div>";
                            } else {
                                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                                $query_user = "INSERT INTO users (username, password, role) VALUES ('$user', '$hashed_pass', 'regular')";
                                
                                if(mysqli_query($koneksi, $query_user)){
                                    $id_user_baru = mysqli_insert_id($koneksi);
                                    if(!empty($minat)){
                                        foreach($minat as $tag){
                                            $tag_safe = mysqli_real_escape_string($koneksi, $tag);
                                            mysqli_query($koneksi, "INSERT INTO user_interests (id_users, tag_name) VALUES ('$id_user_baru', '$tag_safe')");
                                        }
                                    }
                                    // REDIRECT LANGSUNG KE LOGIN
                                    echo "<script>
                                            alert('Pendaftaran Berhasil! Silakan Login.');
                                            window.location.href='login.php';
                                          </script>";
                                }
                            }
                        }
                    }
                    ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">USERNAME</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Username unik kamu" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Minimal 6 karakter" minlength="6" required>
                            </div>
                            <div class="form-text mt-1" style="font-size: 0.75rem;">Keamanan akun adalah prioritas utama.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-primary d-block mb-3">MINAT BERITA ANDA</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $kategori = ['Teknologi', 'Sains', 'Politik', 'Otomotif', 'Gaming', 'Kesehatan', 'Olahraga', 'Nasional'];
                                foreach($kategori as $kat): ?>
                                    <input type="checkbox" class="btn-check" name="minat[]" value="<?php echo $kat; ?>" id="kat_<?php echo $kat; ?>" autocomplete="off">
                                    <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="kat_<?php echo $kat; ?>">
                                        + <?php echo $kat; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-warning w-100 fw-bold py-3 shadow border-0">DAFTAR SEKARANG</button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p class="small text-muted">Sudah punya akun? <a href="login.php" class="fw-bold text-decoration-none">Masuk</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling checkbox minat agar seperti tombol */
    .btn-check:checked + .btn-outline-secondary {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
        font-weight: bold;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #ffc107;
    }
</style>

<?php include 'includes/footer.php'; ?>