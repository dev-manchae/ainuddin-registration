<?php
$currentPage = $_GET['page'] ?? 'home';
$isUtama = ($currentPage === 'home' || $currentPage === '');
$isDashboard = in_array($currentPage, ['dashboard', 'my_applications', 'mula_permohonan', 'step1', 'step2', 'step3', 'step4', 'step5', 'step6', 'resume_permohonan']);
$isProfil = ($currentPage === 'profil');
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ainuddin Tahfiz — Pendaftaran Pelajar</title>
    
    <!-- External Assets -->
    <link rel="stylesheet" href="public/assets/css/main.css">
    
</head>
<body>

<div class="top-nav">
    <a href="?page=home" class="nav-brand">
        <div class="logo-icon" style="background: transparent; box-shadow: none;">
            <img src="public/assets/images/logo.png" alt="Logo Tahfiz Ainuddin" style="width: 40px; height: 40px; object-fit: contain;">
        </div>
        <span class="brand-text">
            Tahfiz Ainuddin
            <small>Pusat Rangkaian</small>
        </span>
    </a>
    
    <button class="menu-toggle" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-links">
        <div class="nav-menu">
            <a href="?page=home" class="nav-link <?= $isUtama ? 'active' : '' ?>">Utama</a>
            <?php if (isset($_SESSION['id_pengguna'])): ?>
                <a href="?page=dashboard" class="nav-link <?= $isDashboard ? 'active' : '' ?>">Dashboard</a>
                <a href="?page=profil" class="nav-link <?= $isProfil ? 'active' : '' ?>">Profil Saya</a>
            <?php else: ?>
                <a href="?page=login" class="nav-link <?= $currentPage === 'login' ? 'active' : '' ?>">Log Masuk</a>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_SESSION['id_pengguna'])): ?>
            <a href="?page=logout" class="btn-nav-logout">Log Keluar</a>
        <?php else: ?>
            <a href="?page=register" class="btn-nav">Daftar</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-container">
<?php require_once "views/layouts/flash_message.php"; ?>