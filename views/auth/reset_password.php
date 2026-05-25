<?php require_once "views/layouts/header.php"; ?>

<div style="max-width:500px; margin:60px auto;">
    <div class="form-card" style="text-align:center;">
        <h2 style="color:var(--primary);">Set Semula Kata Laluan</h2>
        <p style="color:var(--muted); margin-bottom:25px;">Masukkan kata laluan baru anda</p>

        <form method="POST" action="?page=proses_reset">
            <?= csrfField(); ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? ''); ?>">
            
            <div class="form-field" style="text-align:left;">
                <label>Kata Laluan Baru <span style="color: var(--danger);">*</span></label>
                <div class="password-container">
                    <input type="password" name="kata_laluan" id="reset-password" required placeholder="Min 8 aksara" minlength="8"
                           onkeyup="checkCapsLock(event, 'reset-password-caps')">
                    <button type="button" class="peek-toggle"
                            onmousedown="peekPassword('reset-password', 'reset-eye-open', 'reset-eye-closed', true)" 
                            onmouseup="peekPassword('reset-password', 'reset-eye-open', 'reset-eye-closed', false)"
                            onmouseleave="peekPassword('reset-password', 'reset-eye-open', 'reset-eye-closed', false)"
                            ontouchstart="peekPassword('reset-password', 'reset-eye-open', 'reset-eye-closed', true)" 
                            ontouchend="peekPassword('reset-password', 'reset-eye-open', 'reset-eye-closed', false)"
                            aria-label="Tunjuk kata laluan">
                        <svg id="reset-eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg id="reset-eye-closed" viewBox="0 0 24 24" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                <div class="caps-warning" id="reset-password-caps">⚠️ Caps Lock menyala</div>
            </div>

            <div class="form-field" style="text-align:left;">
                <label>Sahkan Kata Laluan Baru <span style="color: var(--danger);">*</span></label>
                <div class="password-container">
                    <input type="password" name="kata_laluan_sahkan" id="reset-confirm-password" required placeholder="Taip semula kata laluan"
                           onkeyup="checkCapsLock(event, 'reset-confirm-caps')">
                    <button type="button" class="peek-toggle"
                            onmousedown="peekPassword('reset-confirm-password', 'reset-confirm-eye-open', 'reset-confirm-eye-closed', true)" 
                            onmouseup="peekPassword('reset-confirm-password', 'reset-confirm-eye-open', 'reset-confirm-eye-closed', false)"
                            onmouseleave="peekPassword('reset-confirm-password', 'reset-confirm-eye-open', 'reset-confirm-eye-closed', false)"
                            ontouchstart="peekPassword('reset-confirm-password', 'reset-confirm-eye-open', 'reset-confirm-eye-closed', true)" 
                            ontouchend="peekPassword('reset-confirm-password', 'reset-confirm-eye-open', 'reset-confirm-eye-closed', false)"
                            aria-label="Tunjuk kata laluan">
                        <svg id="reset-confirm-eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg id="reset-confirm-eye-closed" viewBox="0 0 24 24" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                <div class="caps-warning" id="reset-confirm-caps">⚠️ Caps Lock menyala</div>
            </div>

            <button type="submit" class="btn btn-teal" style="width:100%;">Simpan Kata Laluan</button>
        </form>

        <p style="margin-top:20px; font-size:14px; color:var(--muted);">
            <a href="?page=login" style="color:var(--teal); font-weight:600;">← Kembali ke Log Masuk</a>
        </p>
    </div>
</div>

<style>
    .password-container {
        position: relative;
    }
    .password-container input {
        padding-right: 50px !important;
    }
    .peek-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        line-height: 1;
        color: #94a3b8;
        transition: color 0.2s;
    }
    .peek-toggle:hover {
        color: #475569;
    }
    .peek-toggle svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .caps-warning {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
        display: none;
        font-weight: 500;
    }
    .caps-warning.visible {
        display: block;
    }
</style>

<script>
    function checkCapsLock(event, warningId) {
        const warning = document.getElementById(warningId);
        if (event.getModifierState && event.getModifierState('CapsLock')) {
            warning.classList.add('visible');
        } else {
            warning.classList.remove('visible');
        }
    }

    function peekPassword(inputId, openId, closedId, show) {
        const pwdField = document.getElementById(inputId);
        const eyeOpen = document.getElementById(openId);
        const eyeClosed = document.getElementById(closedId);
        if (show) {
            pwdField.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            pwdField.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>

<?php require_once "views/layouts/footer.php"; ?>