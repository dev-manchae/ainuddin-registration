<?php require_once "views/layouts/header.php"; ?>

<style>
    .login-wrapper {
        max-width: 450px;
        margin: 60px auto;
    }
    .login-card {
        background: white;
        border-radius: 16px;
        padding: 40px 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    .login-card h2 {
        margin-bottom: 5px;
        color: #1e5631;
        font-weight: 700;
    }
    .login-card .subtitle {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 25px;
    }
    .field {
        text-align: left;
        margin-bottom: 20px;
    }
    .field label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
        color: #334155;
    }
    .field input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        background: #fafbfc;
        transition: border 0.2s;
    }
    .field input:focus {
        outline: none;
        border-color: #00897b;
        box-shadow: 0 0 0 3px rgba(0,137,123,0.1);
    }
    .password-container {
        position: relative;
    }
    .password-container input {
        padding-right: 50px;
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
    .btn-login {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        background: #1e5631;
        color: white;
        transition: background 0.2s;
    }
    .btn-login:hover {
        background: #163d26;
    }
    .register-link {
        margin-top: 20px;
        font-size: 14px;
        color: #64748b;
    }
    .register-link a {
        color: #00897b;
        font-weight: 600;
        text-decoration: none;
    }
    .register-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Log Masuk</h2>
        <p class="subtitle">Sila masukkan e-mel dan kata laluan anda</p>

        <form method="POST" action="?page=login_process">
            <?= csrfField(); ?>
            <div class="field">
                <label>E-mel <span style="color: var(--danger);">*</span></label>
                <input type="email" name="emel" id="emel" required placeholder="contoh@gmail.com"
                       onkeyup="checkCapsLock(event, 'email-caps-warning')">
                <div class="caps-warning" id="email-caps-warning">⚠️ Caps Lock menyala – e‑mel mungkin tidak tepat</div>
            </div>

            <div class="field">
                <label>Katalaluan <span style="color: var(--danger);">*</span></label>
                <div class="password-container">
                    <input type="password" name="kata_laluan" id="password" required placeholder="••••••••"
                           onkeyup="checkCapsLock(event, 'password-caps-warning')">
                    <button type="button" class="peek-toggle" id="peek-toggle"
                            onmousedown="peekPassword(true)" onmouseup="peekPassword(false)"
                            onmouseleave="peekPassword(false)"
                            ontouchstart="peekPassword(true)" ontouchend="peekPassword(false)"
                            aria-label="Tunjuk kata laluan">
                        <svg id="eye-open" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg id="eye-closed" viewBox="0 0 24 24" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
                <div class="caps-warning" id="password-caps-warning">⚠️ Caps Lock menyala</div>
            </div>

            <button type="submit" class="btn-login">Log Masuk</button>
        </form>

        <p class="register-link">
            <a href="?page=lupa_kata_laluan" style="color: var(--muted); font-weight: 500;">Lupa kata laluan?</a>
        </p>
        <p class="register-link">
            Belum mempunyai akaun? <a href="?page=register">Daftar di sini</a>
        </p>
    </div>
</div>

<script>
    function checkCapsLock(event, warningId) {
        const warning = document.getElementById(warningId);
        if (event.getModifierState && event.getModifierState('CapsLock')) {
            warning.classList.add('visible');
        } else {
            warning.classList.remove('visible');
        }
    }

    function peekPassword(show) {
        const pwdField = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');
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

    document.addEventListener('keyup', function(e) {
        if (e.target.matches('#password')) {
            checkCapsLock(e, 'password-caps-warning');
        } else if (e.target.matches('#emel')) {
            checkCapsLock(e, 'email-caps-warning');
        }
    });
</script>

<?php require_once "views/layouts/footer.php"; ?>