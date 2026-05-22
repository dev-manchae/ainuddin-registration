<?php require_once "views/layouts/header.php"; ?>

<style>
    .admin-login-container {
        max-width: 420px;
        margin: 80px auto 40px;
    }
    .admin-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    .admin-card h2 {
        margin: 0 0 5px;
        color: #1e293b;
        font-weight: 700;
    }
    .admin-card .subtitle {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 25px;
    }
    .admin-card .field {
        text-align: left;
        margin-bottom: 18px;
    }
    .admin-card .field label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
        color: #334155;
    }
    .admin-card .field input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        background: #fafbfc;
        transition: border 0.2s;
    }
    .admin-card .field input:focus {
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
    .btn-admin {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        background: linear-gradient(135deg, #1e5631, #00897b);
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 5px;
    }
    .btn-admin:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(0,137,123,0.35);
    }
    .admin-link {
        color: #00897b;
        font-weight: 600;
        text-decoration: none;
        font-size: 14px;
    }
    .admin-link:hover {
        text-decoration: underline;
    }
</style>

<div class="admin-login-container">
    <div class="admin-card">
        <img src="public/assets/images/logo.png" alt="Logo Admin" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 15px;">
        <h2>Log Masuk Admin</h2>
        <p class="subtitle">Akses panel pentadbiran Ainuddin</p>

        <form method="POST" action="?page=admin_login_process">
            <?= csrfField(); ?>
            <div class="field">
                <label>Emel</label>
                <input type="email" name="emel" id="admin-emel" placeholder="admin@ainuddin.com" required
                       onkeyup="checkCapsLock(event, 'admin-email-caps')">
                <div class="caps-warning" id="admin-email-caps">⚠️ Caps Lock menyala – e‑mel mungkin tidak tepat</div>
            </div>
            <div class="field">
                <label>Kata Laluan</label>
                <div class="password-container">
                    <input type="password" name="kata_laluan" id="admin-password" placeholder="••••••••" required
                           onkeyup="checkCapsLock(event, 'admin-password-caps')">
                    <button type="button" class="peek-toggle"
                            onmousedown="peekAdminPassword(true)" onmouseup="peekAdminPassword(false)"
                            onmouseleave="peekAdminPassword(false)"
                            ontouchstart="peekAdminPassword(true)" ontouchend="peekAdminPassword(false)"
                            aria-label="Tunjuk kata laluan">
                        <!-- Eye icon (open) -->
                        <svg id="admin-eye-open" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <!-- Eye-off icon (hidden by default) -->
                        <svg id="admin-eye-closed" viewBox="0 0 24 24" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
                <div class="caps-warning" id="admin-password-caps">⚠️ Caps Lock menyala</div>
            </div>
            <button type="submit" class="btn-admin">
                Log Masuk
            </button>
        </form>

        <p style="margin-top: 20px; color: #94a3b8; font-size: 13px;">
            <a href="?page=login" class="admin-link">← Log Masuk Pengguna</a>
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

    function peekAdminPassword(show) {
        const pwdField = document.getElementById('admin-password');
        const eyeOpen = document.getElementById('admin-eye-open');
        const eyeClosed = document.getElementById('admin-eye-closed');
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
        if (e.target.matches('#admin-password')) {
            checkCapsLock(e, 'admin-password-caps');
        } else if (e.target.matches('#admin-emel')) {
            checkCapsLock(e, 'admin-email-caps');
        }
    });
</script>

<?php require_once "views/layouts/footer.php"; ?>