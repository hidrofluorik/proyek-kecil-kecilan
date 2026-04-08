<?php 
// Memanggil koneksi secara aman
include_once __DIR__ . '/db_config.php'; 

// Memastikan session sudah jalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB BERITA - Portal Berita Modern</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar { padding: 0.8rem 0; }
        .navbar-brand { font-size: 1.6rem; letter-spacing: -1px; }

        /* --- CAROUSEL SETTINGS --- */
        .carousel-item img {
            height: 550px; 
            width: 100%;
            object-fit: cover; 
        }
        .navbar {
            z-index: 1050 !important; /* Memaksa Navbar berada di lapisan paling depan */
        }
        .dropdown-menu {
            z-index: 1060 !important;
        }

        .carousel-caption {
            text-align: left;
            left: 0; right: 0; bottom: 0;
            padding: 150px 10% 60px 10%; 
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);
        }

        .carousel-caption h5 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.1;
            text-transform: uppercase;
        }

        @media (max-width: 768px) {
            .carousel-item img { height: 400px; }
            .carousel-caption h5 { font-size: 1.8rem; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
  <div class="container">
    <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
        <b>WEB<span class="text-warning">BERITA</span></b>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>index.php">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>forum.php">Forum Tanya Jawab</a>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <?php if(isset($_SESSION['username'])): ?>
            <div class="dropdown">
              <button class="btn btn-outline-warning dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                Hi, <?php echo $_SESSION['username']; ?> 
                <span class="badge bg-warning text-dark ms-1"><?php echo $_SESSION['points'] ?? 0; ?> pts</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li>
                    <a class="dropdown-item py-2" href="<?php echo $base_url; ?>profil.php">
                        <i class="bi bi-person-circle me-2"></i>Profil Saya
                    </a>
                </li>
                
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2 fw-bold text-primary" href="<?php echo $base_url; ?>admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                        </a>
                    </li>
                <?php endif; ?>

                <?php if($_SESSION['role'] == 'verificator'): ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2 text-success" href="<?php echo $base_url; ?>panel_verifikasi.php">
                            <i class="bi bi-patch-check me-2"></i>Panel Verifikator
                        </a>
                    </li>
                <?php endif; ?>

                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item py-2 text-danger" href="<?php echo $base_url; ?>logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                    </a>
                </li>
              </ul>
            </div>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>login.php" class="btn btn-outline-light btn-sm me-2">Masuk</a>
            <a href="<?php echo $base_url; ?>register.php" class="btn btn-warning btn-sm fw-bold">Daftar</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>