<h2 style="margin-bottom:20px;">Maklumat Peribadi Pelajar</h2>
<form method="POST" action="?page=save_step1" id="stepForm">
    <?= csrfField(); ?>

    <div class="form-field">
        <label>Nama Penuh <span style="color: var(--danger);">*</span></label>
        <input type="text" name="nama_penuh" value="<?= htmlspecialchars($pelajar['nama_penuh'] ?? ''); ?>" required>
    </div>

    <div class="form-field">
        <label>Jantina <span style="color: var(--danger);">*</span></label>
        <select name="jantina" required>
            <option value="">-- Pilih --</option>
            <option value="Lelaki" <?= (($pelajar['jantina']??'') == 'Lelaki')?'selected':'' ?>>Lelaki</option>
            <option value="Perempuan" <?= (($pelajar['jantina']??'') == 'Perempuan')?'selected':'' ?>>Perempuan</option>
        </select>
    </div>

    <div class="form-field">
        <label>No. Kad Pengenalan / Sijil Lahir <span style="color: var(--danger);">*</span></label>
        <input type="text" name="no_kp" id="ic_number" placeholder="041231-08-1234" 
               value="<?= htmlspecialchars($pelajar['no_kp'] ?? ''); ?>" required 
               pattern="\d{6}-\d{2}-\d{4}" title="Format: 041231-08-1234"
               data-ic-format>
    </div>

    <div class="form-field">
        <label>Tarikh Lahir <span style="color: var(--danger);">*</span></label>
        <input type="date" name="tarikh_lahir" value="<?= htmlspecialchars($pelajar['tarikh_lahir'] ?? ''); ?>" required>
    </div>

    <div class="form-field">
        <label>Tempat Lahir <span style="color: var(--danger);">*</span></label>
        <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($pelajar['tempat_lahir'] ?? ''); ?>" required>
    </div>

    <div class="form-field">
        <label>Warganegara <span style="color: var(--danger);">*</span></label>
        <input type="text" name="warganegara" value="<?= htmlspecialchars($pelajar['warganegara'] ?? 'Malaysia'); ?>" required>
    </div>

    <div class="form-field">
        <label>Alamat Penuh <span style="color: var(--danger);">*</span></label>
        <textarea name="alamat" required><?= htmlspecialchars($pelajar['alamat'] ?? ''); ?></textarea>
    </div>

    <div class="form-field">
        <label>Negeri <span style="color: var(--danger);">*</span></label>
        <select name="kod_negeri" required>
            <option value="">-- Pilih Negeri --</option>
            <?php foreach ($negeriList as $negeri): ?>
                <option value="<?= $negeri['kod']; ?>" <?= (($pelajar['kod_negeri']??'') == $negeri['kod'])?'selected':'' ?>><?= $negeri['negeri']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-field">
        <label>Cawangan <span style="color: var(--danger);">*</span></label>
        <select name="kod_cawangan" required>
            <?php foreach ($cawanganList as $cawangan): ?>
                <option value="<?= $cawangan['kod']; ?>" <?= (($pelajar['kod_cawangan']??'') == $cawangan['kod'])?'selected':'' ?>><?= $cawangan['cawangan']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-field">
        <label>Program <span style="color: var(--danger);">*</span></label>
        <select name="kod_program" required>
            <?php foreach ($programList as $program): ?>
                <option value="<?= $program['kod']; ?>" <?= (($pelajar['kod_program']??'') == $program['kod'])?'selected':'' ?>><?= $program['program']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display:none;"><button type="submit">Simpan</button></div>
</form>

<script>
// Auto-format IC number (XXXXXX-XX-XXXX)
const icInput = document.getElementById('ic_number');
if (icInput) {
    icInput.setAttribute('maxlength', '14');
    icInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 12) {
            value = value.substring(0, 12);
        }
        let formatted = '';
        if (value.length > 0) {
            formatted += value.substring(0, Math.min(value.length, 6));
        }
        if (value.length > 6) {
            formatted += '-' + value.substring(6, Math.min(value.length, 8));
        }
        if (value.length > 8) {
            formatted += '-' + value.substring(8, Math.min(value.length, 12));
        }
        this.value = formatted;
    });
}
</script>