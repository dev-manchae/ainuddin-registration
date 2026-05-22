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
        <a href="?page=home">Utama</a>
        
        <?php if (isset($_SESSION['id_pengguna'])): ?>
            <a href="?page=dashboard" class="nav-user-name"><?= htmlspecialchars($_SESSION['nama_penuh']); ?></a>
            <a href="?page=logout" class="btn-nav-logout">Log Keluar</a>
        <?php else: ?>
            <a href="?page=login">Log Masuk</a>
            <a href="?page=register" class="btn-nav">Daftar</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-container">
<?php require_once "views/layouts/flash_message.php"; ?>