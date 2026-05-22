<h2 style="margin-bottom:20px;">Maklumat Penjaga</h2>
<form method="POST" action="?page=save_step2" id="stepForm">
    <?= csrfField(); ?>

    <!-- BAPA -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:25px;">
        <h3 style="color:var(--primary);">Maklumat Bapa</h3>
        <div class="form-field">
            <label>Nama Penuh <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_bapa" value="<?= htmlspecialchars($keluarga['Bapa']['nama_penuh'] ?? ''); ?>" required>
        </div>
        <div class="form-field">
            <label>No. Telefon <span style="color: var(--danger);">*</span></label>
            <div class="phone-wrapper">
                <span class="phone-prefix">+60</span>
                <input type="text" name="telefon_bapa" 
                       value="<?= htmlspecialchars(str_replace('+60', '', $keluarga['Bapa']['no_telefon'] ?? '')); ?>" 
                       required maxlength="12" data-phone-clean>
            </div>
        </div>
        <div class="form-field">
            <label>Pekerjaan <span style="color: var(--danger);">*</span></label>
            <input type="text" name="pekerjaan_bapa" value="<?= htmlspecialchars($keluarga['Bapa']['pekerjaan'] ?? ''); ?>" required>
        </div>
        <div class="form-field">
            <label>Pendapatan (RM) <span style="color: var(--danger);">*</span></label>
            <input type="number" step="0.01" name="pendapatan_bapa" value="<?= htmlspecialchars($keluarga['Bapa']['pendapatan'] ?? ''); ?>" required>
        </div>
        <div class="form-field">
            <label>Alamat <span style="color: var(--danger);">*</span></label>
            <textarea name="alamat_bapa" required><?= htmlspecialchars($keluarga['Bapa']['alamat'] ?? ''); ?></textarea>
        </div>
        <div class="form-field">
            <label>Emel</label>
            <input type="email" name="emel_bapa" value="<?= htmlspecialchars($keluarga['Bapa']['emel'] ?? ''); ?>">
        </div>
    </div>

    <!-- IBU -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:25px;">
        <h3 style="color:var(--primary);">Maklumat Ibu</h3>
        <div class="form-field">
            <label>Nama Penuh <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_ibu" value="<?= htmlspecialchars($keluarga['Ibu']['nama_penuh'] ?? ''); ?>" required>
        </div>
        <div class="form-field">
            <label>No. Telefon <span style="color: var(--danger);">*</span></label>
            <div class="phone-wrapper">
                <span class="phone-prefix">+60</span>
                <input type="text" name="telefon_ibu" 
                       value="<?= htmlspecialchars(str_replace('+60', '', $keluarga['Ibu']['no_telefon'] ?? '')); ?>" 
                       required maxlength="12" data-phone-clean>
            </div>
        </div>
        <div class="form-field">
            <label>Pekerjaan <span style="color: var(--danger);">*</span></label>
            <input type="text" name="pekerjaan_ibu" value="<?= htmlspecialchars($keluarga['Ibu']['pekerjaan'] ?? ''); ?>" required>
        </div>
        <div class="form-field">
            <label>Pendapatan (RM) <span style="color: var(--danger);">*</span></label>
            <input type="number" step="0.01" name="pendapatan_ibu" value="<?= htmlspecialchars($keluarga['Ibu']['pendapatan'] ?? ''); ?>" required>
        </div>
        <div class="form-field">
            <label>Alamat <span style="color: var(--danger);">*</span></label>
            <textarea name="alamat_ibu" required><?= htmlspecialchars($keluarga['Ibu']['alamat'] ?? ''); ?></textarea>
        </div>
        <div class="form-field">
            <label>Emel</label>
            <input type="email" name="emel_ibu" value="<?= htmlspecialchars($keluarga['Ibu']['emel'] ?? ''); ?>">
        </div>
    </div>

    <div style="display:none;"><button type="submit">Simpan</button></div>
</form>
