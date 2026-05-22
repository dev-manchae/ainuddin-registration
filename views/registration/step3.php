<?php
// Extract surah_hafazan text from combined JSON (backward compatible with plain text)
$surahHafazanText = '';
if (!empty($akademik['surah_hafazan'])) {
    $decoded = json_decode($akademik['surah_hafazan'], true);
    if (is_array($decoded) && isset($decoded['surah_hafazan'])) {
        $surahHafazanText = $decoded['surah_hafazan'];
    } else {
        $surahHafazanText = $akademik['surah_hafazan'];
    }
}
?>
<h2 style="margin-bottom:20px;">Akademik & Hafazan</h2>
<form method="POST" action="?page=save_step3" id="stepForm">
    <?= csrfField(); ?>

    <div class="form-field">
        <label>Nama Sekolah Terdahulu <span style="color: var(--danger);">*</span></label>
        <input type="text" name="nama_sekolah" value="<?= htmlspecialchars($akademik['nama_sekolah'] ?? ''); ?>" required>
    </div>

    <div class="form-field">
        <label>Tahap Penguasaan Al-Quran <span style="color: var(--danger);">*</span></label>
        <select name="tahap_quran" required>
            <option value="">-- Pilih --</option>
            <?php
            $tahapList = ['Iqra','Muqaddam','Lancar Membaca','Hafiz Sebahagian','Hafiz 30 Juzuk'];
            foreach ($tahapList as $tahap):
            ?>
                <option value="<?= $tahap; ?>" <?= (($akademik['tahap_quran']??'') == $tahap)?'selected':'' ?>><?= $tahap; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-field">
        <label>Status Khatam <span style="color: var(--danger);">*</span></label>
        <select name="status_khatam" required>
            <option value="">-- Pilih --</option>
            <option value="Sudah Khatam" <?= (($akademik['status_khatam']??'') == 'Sudah Khatam')?'selected':'' ?>>Sudah Khatam</option>
            <option value="Belum Khatam" <?= (($akademik['status_khatam']??'') == 'Belum Khatam')?'selected':'' ?>>Belum Khatam</option>
        </select>
    </div>

    <div class="form-field">
        <label>Surah Hafazan (Jika Ada)</label>
        <textarea name="surah_hafazan"><?= htmlspecialchars($surahHafazanText ?? ''); ?></textarea>
    </div>

    <!-- DYNAMIC AKADEMIK -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <label style="font-weight:600; margin:0;">Keputusan Akademik (Akademik)</label>
            <button type="button" class="btn-add-row" onclick="addRow('akademik')">+ Tambah</button>
        </div>
        <div style="overflow-x: auto;">
            <table class="dynamic-table" id="table-akademik" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40%;">Subjek</th>
                        <th style="width: 40%;">Keputusan (Gred)</th>
                        <th style="width: 20%;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $akademikData = [];
                    if (!empty($akademik['keputusan_akademik'])) {
                        $akademikData = json_decode($akademik['keputusan_akademik'], true) ?: [];
                    }
                    if (empty($akademikData)): ?>
                        <tr class="dynamic-row">
                            <td><input type="text" name="subjek_akademik[]" placeholder="cth: Bahasa Melayu"></td>
                            <td><input type="text" name="keputusan_akademik[]" placeholder="cth: A"></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Padam</button></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($akademikData as $item): ?>
                        <tr class="dynamic-row">
                            <td><input type="text" name="subjek_akademik[]" value="<?= htmlspecialchars($item['subjek'] ?? ''); ?>"></td>
                            <td><input type="text" name="keputusan_akademik[]" value="<?= htmlspecialchars($item['keputusan'] ?? ''); ?>"></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Padam</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DYNAMIC SEKOLAH AGAMA -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <label style="font-weight:600; margin:0;">Keputusan Sekolah Agama</label>
            <button type="button" class="btn-add-row" onclick="addRow('agama')">+ Tambah</button>
        </div>
        <div style="overflow-x: auto;">
            <table class="dynamic-table" id="table-agama" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40%;">Subjek</th>
                        <th style="width: 40%;">Keputusan (Gred)</th>
                        <th style="width: 20%;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $agamaData = [];
                    if (!empty($akademik['surah_hafazan'])) {
                        $decoded = json_decode($akademik['surah_hafazan'], true);
                        if (is_array($decoded) && isset($decoded['keputusan_agama'])) {
                            $agamaData = $decoded['keputusan_agama'];
                        }
                    }
                    if (empty($agamaData)): ?>
                        <tr class="dynamic-row">
                            <td><input type="text" name="subjek_agama[]" placeholder="cth: Ulum Syariah"></td>
                            <td><input type="text" name="keputusan_agama[]" placeholder="cth: Jayyid"></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Padam</button></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agamaData as $item): ?>
                        <tr class="dynamic-row">
                            <td><input type="text" name="subjek_agama[]" value="<?= htmlspecialchars($item['subjek'] ?? ''); ?>"></td>
                            <td><input type="text" name="keputusan_agama[]" value="<?= htmlspecialchars($item['keputusan'] ?? ''); ?>"></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Padam</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:none;"><button type="submit">Simpan</button></div>
</form>

<style>
    .dynamic-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .dynamic-table th, .dynamic-table td { border: 1px solid var(--border); padding: 8px; text-align: left; }
    .dynamic-table th { background: #f8fafc; font-size: 13px; font-weight: 600; }
    .dynamic-table input { width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 4px; font-size: 13px; background: white; }
    .dynamic-table input:focus { outline: none; border-color: var(--teal); }
    .btn-add-row { background: var(--primary); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; cursor: pointer; font-weight: 600; }
    .btn-add-row:hover { background: var(--primary-dark); }
    .btn-remove-row { background: #fee2e2; color: #b91c1c; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: 600; }
    .btn-remove-row:hover { background: #fecaca; }
</style>

<script>
function addRow(type) {
    var table = document.getElementById('table-' + type).querySelector('tbody');
    var row = document.createElement('tr');
    row.classList.add('dynamic-row');
    
    if (type === 'akademik') {
        row.innerHTML = `<td><input type="text" name="subjek_akademik[]" placeholder="cth: Bahasa Melayu"></td>
                         <td><input type="text" name="keputusan_akademik[]" placeholder="cth: A"></td>
                         <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Padam</button></td>`;
    } else if (type === 'agama') {
        row.innerHTML = `<td><input type="text" name="subjek_agama[]" placeholder="cth: Ulum Syariah"></td>
                         <td><input type="text" name="keputusan_agama[]" placeholder="cth: Jayyid"></td>
                         <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">Padam</button></td>`;
    }
    table.appendChild(row);
}

function removeRow(btn) {
    var row = btn.parentNode.parentNode;
    if (row.parentNode.rows.length > 1) {
        row.parentNode.removeChild(row);
    } else {
        alert("Sekurang-kurangnya satu baris diperlukan.");
    }
}
</script>