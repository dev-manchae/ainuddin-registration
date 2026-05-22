<?php require_once "views/layouts/header.php"; ?>

<div style="max-width:500px; margin:60px auto;">
    <div class="form-card" style="text-align:center;">
        <h2 style="color:var(--primary);">Lupa Kata Laluan</h2>
        <p style="color:var(--muted); margin-bottom:25px;">Masukkan e-mel anda untuk menerima pautan set semula</p>

        <form method="POST" action="?page=proses_lupa">
            <?= csrfField(); ?>
            <div class="form-field" style="text-align:left;">
                <label>E-mel Pendaftaran <span style="color: var(--danger);">*</span></label>
                <input type="email" name="emel" required placeholder="contoh@gmail.com">
            </div>
            <button type="submit" class="btn btn-teal" style="width:100%;">Hantar Pautan</button>
        </form>

        <p style="margin-top:20px; font-size:14px; color:var(--muted);">
            <a href="?page=login" style="color:var(--teal); font-weight:600;">← Kembali ke Log Masuk</a>
        </p>
    </div>
</div>

<?php require_once "views/layouts/footer.php"; ?>