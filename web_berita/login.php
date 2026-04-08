<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="text-center fw-bold mb-4">MASUK</h3>

                    <?php 
                    if(isset($_POST['login'])){
                        $user = mysqli_real_escape_string($koneksi, $_POST['username']);
                        $pass = $_POST['password'];

                        // Cari user berdasarkan username
                        $query  = "SELECT * FROM users WHERE username = '$user'";
                        $result = mysqli_query($koneksi, $query);

                        if(mysqli_num_rows($result) === 1){
                            $row = mysqli_fetch_assoc($result);
                            if(password_verify($pass, $row['password'])){
    // Pastikan session start sudah terpanggil (biasanya sudah di header, tapi buat jaga-jaga)
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    // COCOKKAN INI DENGAN NAMA KOLOM DI DATABASE KAMU
    // Kalau di phpMyAdmin kolomnya namanya 'id', pakai $row['id']
    $_SESSION['id_users'] = $row['id']; 
    
    $_SESSION['username'] = $row['username'];
    $_SESSION['role']     = $row['role'];
    $_SESSION['points']   = $row['reputation_points'] ?? 0;

    echo "<script>alert('Selamat Datang, ".$row['username']."!'); window.location='index.php';</script>";
    exit;


                                // Redirect (Pindahkan halaman)
                                echo "<script>alert('Selamat Datang, ".$row['username']."!'); window.location='index.php';</script>";
                                exit;
                            }
                        }
                        echo "<div class='alert alert-danger py-2 small text-center'>Username atau Password salah!</div>";
                    }
                    ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username..." required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
                        </div>
                        <button type="submit" name="login" class="btn btn-dark w-100 fw-bold text-warning">MASUK SEKARANG</button>
                    </form>
                    
                    <div class="text-center mt-3 small">
                        Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>