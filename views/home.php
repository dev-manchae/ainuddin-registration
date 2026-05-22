<?php require_once "views/layouts/header.php"; ?>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="hero-content">
        <span class="hero-badge">Pendaftaran Terbuka</span>
        <h1 class="hero-title">
            Mendidik Generasi<br>
            <span>Hafiz Al-Quran</span>
        </h1>
        <p class="hero-subtitle">
            Menyediakan persekitaran pembelajaran yang kondusif dengan kurikulum tahfiz moden 
            dan kurikulum kebangsaan yang seimbang.
        </p>
        <div class="hero-buttons">
            <a href="?page=register" class="btn-hero-primary">Mohon Sekarang</a>
            <a href="#kenapa-kami" class="btn-hero-secondary">Ketahui Lebih Lanjut</a>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="section-padding" id="kenapa-kami" style="background: white;">
    <div class="section-header">
        <span class="section-label">Kenapa Pilih Kami</span>
        <h2 class="section-title">Kelebihan Tahfiz Ainuddin</h2>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📖</div>
            <h3 class="feature-title">Kurikulum Bersepadu</h3>
            <p class="feature-text">
                Gabungan sempurna antara sistem tahfiz tradisional dan akademik moden (KSSM) untuk masa depan yang cemerlang.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">👨‍🏫</div>
            <h3 class="feature-title">Guru Berpengalaman</h3>
            <p class="feature-text">
                Ditadbir oleh para hafiz dan ustaz/ustazah yang berkelayakan tinggi serta berpengalaman luas dalam bidang pendidikan.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🏡</div>
            <h3 class="feature-title">Fasiliti Selesa</h3>
            <p class="feature-text">
                Bilik asrama yang selesa, persekitaran yang tenang, dan kemudahan pembelajaran yang lengkap untuk pelajar.
            </p>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="section-padding" style="background: var(--light-bg); text-align: center;">
    <div class="section-header">
        <span class="section-label">Mula Perjalanan Anda</span>
        <h2 class="section-title">Sedia Menjadi Keluarga Kami?</h2>
    </div>
    <p style="max-width: 600px; margin: 0 auto 30px; color: var(--muted);">
        Daftarkan anak anda atau diri anda sendiri hari ini. Proses pendaftaran kami adalah dalam talian dan mudah.
    </p>
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="?page=register" class="btn btn-teal" style="padding: 16px 40px; font-size: 16px;">Buka Akaun Baru</a>
        <?php if (isset($_SESSION['id_pengguna'])): ?>
            <a href="?page=dashboard" class="btn btn-outline" style="padding: 16px 40px; font-size: 16px;">Pergi Ke Dashboard</a>
        <?php else: ?>
            <a href="?page=login" class="btn btn-outline" style="padding: 16px 40px; font-size: 16px;">Saya Sudah Ada Akaun</a>
        <?php endif; ?>
    </div>
</section>

<?php require_once "views/layouts/footer.php"; ?>