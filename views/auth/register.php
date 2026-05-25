<?php require_once "views/layouts/header.php"; ?>

<div style="max-width:500px; margin:60px auto;">
    <div class="form-card" style="text-align:center;">
        <h2 style="color:var(--primary);">Daftar Akaun</h2>
        <p style="color:var(--muted); margin-bottom:25px;">Isi butiran untuk mendaftar</p>

        <form method="POST" action="?page=register_process">
            <?= csrfField(); ?>
            <div class="form-field" style="text-align:left;">
                <label>Nama Penuh <span style="color: var(--danger);">*</span></label>
                <input type="text" name="nama_penuh" required placeholder="Nama penuh anda">
            </div>
            <div class="form-field" style="text-align:left;">
                <label>E-mel (Gmail sahaja) <span style="color: var(--danger);">*</span></label>
                <input type="email" name="emel" id="reg-emel" required placeholder="contoh@gmail.com"
                       onkeyup="checkCapsLock(event, 'reg-email-caps')">
                <div class="caps-warning" id="reg-email-caps">⚠️ Caps Lock menyala</div>
            </div>
            <div class="form-field" style="text-align:left;">
                <label>No. Telefon <span style="color: var(--danger);">*</span></label>
                <div class="phone-wrapper">
                    <span class="phone-prefix">+60</span>
                    <input type="text" name="no_telefon" required placeholder="12 3456 7890" maxlength="12" data-phone-clean>
                </div>
            </div>
            <div class="form-field" style="text-align:left;">
                <label>Kata Laluan <span style="color: var(--danger);">*</span></label>
                <div class="password-container">
                    <input type="password" name="kata_laluan" id="reg-password" required placeholder="Min 8 aksara" minlength="8"
                           onkeyup="checkCapsLock(event, 'reg-password-caps')">
                    <button type="button" class="peek-toggle"
                            onmousedown="peekPassword('reg-password', 'reg-eye-open', 'reg-eye-closed', true)" 
                            onmouseup="peekPassword('reg-password', 'reg-eye-open', 'reg-eye-closed', false)"
                            onmouseleave="peekPassword('reg-password', 'reg-eye-open', 'reg-eye-closed', false)"
                            ontouchstart="peekPassword('reg-password', 'reg-eye-open', 'reg-eye-closed', true)" 
                            ontouchend="peekPassword('reg-password', 'reg-eye-open', 'reg-eye-closed', false)"
                            aria-label="Tunjuk kata laluan">
                        <svg id="reg-eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg id="reg-eye-closed" viewBox="0 0 24 24" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                <div class="caps-warning" id="reg-password-caps">⚠️ Caps Lock menyala</div>
            </div>
            <div class="form-field" style="text-align:left;">
                <label>Sahkan Kata Laluan <span style="color: var(--danger);">*</span></label>
                <div class="password-container">
                    <input type="password" name="kata_laluan_sahkan" id="reg-confirm-password" required placeholder="Taip semula kata laluan"
                           onkeyup="checkCapsLock(event, 'reg-confirm-caps')">
                    <button type="button" class="peek-toggle"
                            onmousedown="peekPassword('reg-confirm-password', 'reg-confirm-eye-open', 'reg-confirm-eye-closed', true)" 
                            onmouseup="peekPassword('reg-confirm-password', 'reg-confirm-eye-open', 'reg-confirm-eye-closed', false)"
                            onmouseleave="peekPassword('reg-confirm-password', 'reg-confirm-eye-open', 'reg-confirm-eye-closed', false)"
                            ontouchstart="peekPassword('reg-confirm-password', 'reg-confirm-eye-open', 'reg-confirm-eye-closed', true)" 
                            ontouchend="peekPassword('reg-confirm-password', 'reg-confirm-eye-open', 'reg-confirm-eye-closed', false)"
                            aria-label="Tunjuk kata laluan">
                        <svg id="reg-confirm-eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg id="reg-confirm-eye-closed" viewBox="0 0 24 24" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                <div class="caps-warning" id="reg-confirm-caps">⚠️ Caps Lock menyala</div>
            </div>
            <button type="submit" class="btn btn-teal" style="width:100%;">Daftar</button>
        </form>

        <p style="margin-top:20px; font-size:14px; color:var(--muted);">
            Sudah mempunyai akaun? <a href="?page=login" style="color:var(--teal); font-weight:600;">Log Masuk</a>
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