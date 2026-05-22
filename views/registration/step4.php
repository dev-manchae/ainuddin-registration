<?php
$alahan_val = $kesihatan['alahan'] ?? 'Tiada';
$alahan_is_tiada = ($alahan_val === 'Tiada' || $alahan_val === '');

$penyakit_val = $kesihatan['penyakit_kronik'] ?? 'Tiada';
$penyakit_is_tiada = ($penyakit_val === 'Tiada' || $penyakit_val === '');

$ubat_val = $kesihatan['pengambilan_ubat'] ?? 'Tiada';
$ubat_is_tiada = ($ubat_val === 'Tiada' || $ubat_val === '');
?>
<h2 style="margin-bottom:20px;">Maklumat Kesihatan</h2>
<form method="POST" action="?page=save_step4" id="stepForm">
    <?= csrfField(); ?>

    <!-- Alahan -->
    <div class="health-card">
        <label class="health-label">Alahan</label>
        <div class="health-toggle-group">
            <label class="toggle-option <?= $alahan_is_tiada ? 'active' : '' ?>">
                <input type="radio" name="alahan_toggle" value="Tiada" <?= $alahan_is_tiada ? 'checked' : '' ?>>
                <span>Tiada</span>
            </label>
            <label class="toggle-option <?= !$alahan_is_tiada ? 'active' : '' ?>">
                <input type="radio" name="alahan_toggle" value="Ada" <?= !$alahan_is_tiada ? 'checked' : '' ?>>
                <span>Ada</span>
            </label>
        </div>
        <textarea name="alahan" class="health-textarea" placeholder="Nyatakan alahan (jika Ada)" <?= $alahan_is_tiada ? 'disabled' : '' ?>><?= htmlspecialchars($alahan_is_tiada ? '' : $alahan_val) ?></textarea>
    </div>

    <!-- Penyakit Kronik -->
    <div class="health-card">
        <label class="health-label">Penyakit Kronik</label>
        <div class="health-toggle-group">
            <label class="toggle-option <?= $penyakit_is_tiada ? 'active' : '' ?>">
                <input type="radio" name="penyakit_toggle" value="Tiada" <?= $penyakit_is_tiada ? 'checked' : '' ?>>
                <span>Tiada</span>
            </label>
            <label class="toggle-option <?= !$penyakit_is_tiada ? 'active' : '' ?>">
                <input type="radio" name="penyakit_toggle" value="Ada" <?= !$penyakit_is_tiada ? 'checked' : '' ?>>
                <span>Ada</span>
            </label>
        </div>
        <textarea name="penyakit_kronik" class="health-textarea" placeholder="Nyatakan penyakit kronik (jika Ada)" <?= $penyakit_is_tiada ? 'disabled' : '' ?>><?= htmlspecialchars($penyakit_is_tiada ? '' : $penyakit_val) ?></textarea>
    </div>

    <!-- Ubat Semasa -->
    <div class="health-card">
        <label class="health-label">Ubat Semasa</label>
        <div class="health-toggle-group">
            <label class="toggle-option <?= $ubat_is_tiada ? 'active' : '' ?>">
                <input type="radio" name="ubat_toggle" value="Tiada" <?= $ubat_is_tiada ? 'checked' : '' ?>>
                <span>Tiada</span>
            </label>
            <label class="toggle-option <?= !$ubat_is_tiada ? 'active' : '' ?>">
                <input type="radio" name="ubat_toggle" value="Ada" <?= !$ubat_is_tiada ? 'checked' : '' ?>>
                <span>Ada</span>
            </label>
        </div>
        <textarea name="pengambilan_ubat" class="health-textarea" placeholder="Nyatakan ubat (jika Ada)" <?= $ubat_is_tiada ? 'disabled' : '' ?>><?= htmlspecialchars($ubat_is_tiada ? '' : $ubat_val) ?></textarea>
    </div>

    <!-- Nombor Kecemasan with +60 prefix -->
    <div class="form-field">
        <label>Nombor Kecemasan <span style="color: var(--danger);">*</span></label>
        <div class="phone-wrapper">
            <span class="phone-prefix">+60</span>
            <input type="text" name="nombor_kecemasan" 
                   value="<?= htmlspecialchars(str_replace('+60', '', $kesihatan['nombor_kecemasan'] ?? '')); ?>" 
                   required maxlength="12" data-phone-clean>
        </div>
    </div>

    <div class="form-field">
        <label>Kebenaran Rawatan <span style="color: var(--danger);">*</span></label>
        <select name="kebenaran_rawatan" required>
            <option value="">-- Pilih --</option>
            <option value="Ya" <?= (($kesihatan['kebenaran_rawatan'] ?? '') == 'Ya') ? 'selected' : '' ?>>Ya, benarkan rawatan</option>
            <option value="Tidak" <?= (($kesihatan['kebenaran_rawatan'] ?? '') == 'Tidak') ? 'selected' : '' ?>>Tidak</option>
        </select>
    </div>

    <div style="display:none;"><button type="submit">Simpan</button></div>
</form>

<style>
.health-card {
    border: 1px solid var(--border);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
}
.health-label {
    font-weight: 600;
    display: block;
    margin-bottom: 12px;
}
.health-toggle-group {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}
.toggle-option {
    flex: 1;
    text-align: center;
    cursor: pointer;
}
.toggle-option input {
    display: none;
}
.toggle-option span {
    display: block;
    padding: 10px;
    background: #f1f5f9;
    border: 1px solid var(--border);
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.2s;
}
.toggle-option.active span,
.toggle-option input:checked + span {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
.health-textarea {
    width: 100%;
    height: 100px;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    resize: vertical;
}
.health-textarea:disabled {
    background: #f1f5f9;
    color: #94a3b8;
}
</style>

<script>
// Handle toggle behaviour for each health section
document.querySelectorAll('.health-card').forEach(card => {
    const radios = card.querySelectorAll('input[type="radio"]');
    const textarea = card.querySelector('textarea');
    if (!radios.length || !textarea) return;

    let isInitial = true;

    const updateTextarea = () => {
        const selected = card.querySelector('input[type="radio"]:checked');
        
        radios.forEach(radio => {
            const label = radio.closest('.toggle-option');
            if (label) {
                if (radio.checked) {
                    label.classList.add('active');
                } else {
                    label.classList.remove('active');
                }
            }
        });

        if (selected && selected.value === 'Ada') {
            textarea.disabled = false;
            textarea.placeholder = "Nyatakan butiran...";
        } else {
            textarea.disabled = true;
            if (!isInitial) {
                textarea.value = '';
            }
            textarea.placeholder = "(Tiada)";
        }
    };

    radios.forEach(radio => radio.addEventListener('change', () => {
        isInitial = false;
        updateTextarea();
    }));
    updateTextarea(); // initial state
    isInitial = false;
});

</script>