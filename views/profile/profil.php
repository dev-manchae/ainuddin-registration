<style>
    /* Custom style system to bridge Parent and Admin layouts */
    .profile-container {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        align-items: start;
        margin-top: 25px;
    }
    
    .profile-card {
        background: #ffffff !important;
        padding: 35px 30px !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
        border: 1px solid #e2e8f0 !important;
        flex: 1;
        min-width: 340px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .profile-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06) !important;
    }
    
    .profile-card h3 {
        margin-top: 0 !important;
        margin-bottom: 25px !important;
        font-size: 18px !important;
        color: #1e5631 !important;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700 !important;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
    }

    .form-group-custom {
        margin-bottom: 22px;
        position: relative;
    }
    
    .form-group-custom label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        text-align: left;
    }
    
    .form-group-custom input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    
    .form-group-custom input:focus {
        outline: none;
        border-color: #00897b;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(0, 137, 123, 0.1);
    }

    .btn-profile {
        display: inline-block;
        width: 100%;
        padding: 13px 24px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        border: none;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-profile-primary {
        background: #00897b;
        color: white !important;
        box-shadow: 0 4px 12px rgba(0, 137, 123, 0.15);
    }
    
    .btn-profile-primary:hover {
        background: #00796b;
        box-shadow: 0 6px 18px rgba(0, 137, 123, 0.25);
        transform: translateY(-1px);
    }
    
    .btn-profile-secondary {
        background: #1e293b;
        color: white !important;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.15);
    }
    
    .btn-profile-secondary:hover {
        background: #0f172a;
        box-shadow: 0 6px 18px rgba(30, 41, 59, 0.25);
        transform: translateY(-1px);
    }

    .strength-meter-container {
        margin-top: 10px;
    }

    .strength-meter-bars {
        display: flex;
        height: 6px;
        gap: 6px;
        margin-bottom: 6px;
    }

    .strength-bar {
        flex: 1;
        background: #e2e8f0;
        border-radius: 3px;
        transition: background 0.3s ease;
    }

    @media (max-width: 768px) {
        .profile-container {
            flex-direction: column;
        }
        .profile-card {
            width: 100%;
        }
    }
</style>
<div class="student-header">
    <div>
        <h2>Profil Saya</h2>
        <div class="subtext">Urus maklumat peribadi dan tetapan keselamatan akaun anda</div>
    </div>
</div>

<div class="profile-container">
    
    <!-- CARD 1: PERSONAL INFO -->
    <div class="profile-card">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Maklumat Peribadi
        </h3>

        <form method="POST" action="?page=profil_update_info" id="profileInfoForm">
            <?= csrfField(); ?>

            <div class="form-group-custom">
                <label for="nama_penuh">Nama Penuh</label>
                <input 
                    type="text" 
                    id="nama_penuh" 
                    name="nama_penuh" 
                    required 
                    value="<?= htmlspecialchars($user['nama_penuh'] ?? ''); ?>"
                    placeholder="Nama Penuh"
                >
            </div>

            <div class="form-group-custom">
                <label for="emel">Alamat Emel <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">(Tidak boleh diubah)</span></label>
                <input 
                    type="email" 
                    id="emel" 
                    name="emel" 
                    disabled 
                    value="<?= htmlspecialchars($user['emel'] ?? ''); ?>"
                    style="background: #e2e8f0; color: #64748b; cursor: not-allowed; border-color: #cbd5e1;"
                >
            </div>

            <div class="form-group-custom">
                <label for="no_telefon">Nombor Telefon</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 12px; color: #64748b; font-size: 14px; font-weight: 600;">+60</span>
                    <input 
                        type="text" 
                        id="no_telefon" 
                        name="no_telefon" 
                        required 
                        value="<?= htmlspecialchars(substr($user['no_telefon'] ?? '', 3)); ?>"
                        placeholder="12 345 6789"
                        style="padding-left: 45px;"
                    >
                </div>
                <small style="color: #64748b; font-size: 11px; margin-top: 6px; display: block;">Masukkan nombor tanpa kod negara +60.</small>
            </div>

            <button type="submit" class="btn-profile btn-profile-primary" style="margin-top: 10px;">Simpan Perubahan</button>
        </form>
    </div>

    <!-- CARD 2: PASSWORD CONFIG -->
    <div class="profile-card">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Keselamatan Kata Laluan
        </h3>

        <form method="POST" action="?page=profil_update_password" id="passwordForm">
            <?= csrfField(); ?>

            <div class="form-group-custom">
                <label for="kata_laluan_semasa">Kata Laluan Semasa</label>
                <input 
                    type="password" 
                    id="kata_laluan_semasa" 
                    name="kata_laluan_semasa" 
                    required 
                    placeholder="Masukkan kata laluan sekarang"
                >
            </div>

            <div class="form-group-custom" style="margin-bottom: 12px;">
                <label for="kata_laluan_baru">Kata Laluan Baru</label>
                <input 
                    type="password" 
                    id="kata_laluan_baru" 
                    name="kata_laluan_baru" 
                    required 
                    placeholder="Minimum 8 aksara"
                >
                
                <!-- Password Strength Visual Component -->
                <div class="strength-meter-container">
                    <div class="strength-meter-bars">
                        <div id="bar1" class="strength-bar"></div>
                        <div id="bar2" class="strength-bar"></div>
                        <div id="bar3" class="strength-bar"></div>
                    </div>
                    <span id="strengthText" style="font-size: 11px; font-weight: 600; color: #94a3b8; display: block; margin-top: 5px; text-align: right; min-height: 16px;">Sila masukkan kata laluan</span>
                </div>
            </div>

            <div class="form-group-custom">
                <label for="kata_laluan_sahkan">Sahkan Kata Laluan Baru</label>
                <input 
                    type="password" 
                    id="kata_laluan_sahkan" 
                    name="kata_laluan_sahkan" 
                    required 
                    placeholder="Ulang kata laluan baru"
                >
            </div>

            <button type="submit" class="btn-profile btn-profile-secondary" style="margin-top: 10px;">Kemaskini Kata Laluan</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. Phone number formatter ---
    const phoneInput = document.getElementById('no_telefon');
    
    function formatPhone(val) {
        let cleaned = val.replace(/\D/g, '');
        if (cleaned.startsWith('60')) {
            cleaned = cleaned.substring(2);
        }
        if (cleaned.startsWith('0')) {
            cleaned = cleaned.substring(1);
        }
        
        if (cleaned.length === 0) return '';
        if (cleaned.length <= 2) return cleaned;
        if (cleaned.length <= 5) return cleaned.substring(0, 2) + ' ' + cleaned.substring(2);
        return cleaned.substring(0, 2) + ' ' + cleaned.substring(2, 5) + ' ' + cleaned.substring(5, 10);
    }
    
    phoneInput.addEventListener('input', function(e) {
        const start = this.selectionStart;
        const previousLength = this.value.length;
        
        const formatted = formatPhone(this.value);
        this.value = formatted;
        
        // Adjust cursor position
        const currentLength = this.value.length;
        this.selectionStart = this.selectionEnd = start + (currentLength - previousLength);
    });

    // Run format on initial load
    phoneInput.value = formatPhone(phoneInput.value);

    // --- 2. Password Strength Check ---
    const passwordInput = document.getElementById('kata_laluan_baru');
    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');
    const strengthText = document.getElementById('strengthText');
    
    passwordInput.addEventListener('input', function() {
        const val = this.value;
        
        if (val.length === 0) {
            bar1.style.background = '#e2e8f0';
            bar2.style.background = '#e2e8f0';
            bar3.style.background = '#e2e8f0';
            strengthText.textContent = 'Sila masukkan kata laluan';
            strengthText.style.color = '#94a3b8';
            return;
        }
        
        let score = 0;
        
        // Criteria 1: Length
        if (val.length >= 8) {
            score++;
        }
        
        // Criteria 2: Letters + Numbers
        if (/[a-zA-Z]/.test(val) && /\d/.test(val)) {
            score++;
        }
        
        // Criteria 3: Mixed Case & Special Char
        if (/[A-Z]/.test(val) && /[^a-zA-Z0-9]/.test(val)) {
            score++;
        }
        
        // Visual indicators mapping
        if (val.length < 8) {
            // Force Weak if under 8 characters
            bar1.style.background = '#ef4444'; // Red
            bar2.style.background = '#e2e8f0';
            bar3.style.background = '#e2e8f0';
            strengthText.textContent = 'Lemah (Mesti sekurang-kurangnya 8 aksara)';
            strengthText.style.color = '#ef4444';
        } else {
            if (score === 1) {
                bar1.style.background = '#ef4444'; // Red
                bar2.style.background = '#e2e8f0';
                bar3.style.background = '#e2e8f0';
                strengthText.textContent = 'Lemah';
                strengthText.style.color = '#ef4444';
            } else if (score === 2) {
                bar1.style.background = '#f59e0b'; // Amber
                bar2.style.background = '#f59e0b';
                bar3.style.background = '#e2e8f0';
                strengthText.textContent = 'Sederhana';
                strengthText.style.color = '#f59e0b';
            } else if (score === 3) {
                bar1.style.background = '#10b981'; // Green
                bar2.style.background = '#10b981';
                bar3.style.background = '#10b981';
                strengthText.textContent = 'Kuat';
                strengthText.style.color = '#10b981';
            }
        }
    });
});
</script>
