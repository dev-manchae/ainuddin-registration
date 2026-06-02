<?php
// views/admin/email_templates.php
if (!isset($templates) || !isset($activeTemplate)) {
    echo "<div class='alert alert-error'>Ralat memuatkan templat emel.</div>";
    return;
}

$keyNames = [
    'pendaftaran_diterima' => 'Permohonan Diterima (Parent)',
    'permohonan_diluluskan' => 'Permohonan Diluluskan (Parent)',
    'pembetulan_diperlukan' => 'Pembetulan Diperlukan (Parent)',
    'permohonan_ditolak' => 'Permohonan Ditolak (Parent)'
];
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="margin-bottom: 5px;">Editor Templat Emel</h2>
        <p style="color: #64748b; font-size: 14px;">Kemaskini subjek dan kandungan simulasi emel notifikasi sistem.</p>
    </div>
</div>

<style>
.editor-layout {
    display: grid;
    grid-template-columns: 2.5fr 1fr;
    gap: 25px;
}
.token-badge {
    display: inline-block;
    background: #e2e8f0;
    color: #334155;
    padding: 3px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
    margin-bottom: 6px;
    font-weight: 600;
}
.token-desc {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 12px;
    line-height: 1.3;
}
@media (max-width: 991px) {
    .editor-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="editor-layout">
    <!-- BORANG EDIT TEMPLAT -->
    <div class="card">
        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="template_select" style="font-weight: 700; font-size: 14px; color: #334155;">Pilih Templat:</label>
                <select id="template_select" onchange="switchTemplate(this.value)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px;">
                    <?php foreach ($templates as $t): ?>
                        <option value="<?= $t['template_key']; ?>" <?= $t['template_key'] === $activeTemplate['template_key'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($keyNames[$t['template_key']] ?? $t['template_key']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <span style="font-size: 11px; color: #94a3b8;">Terakhir Dikemaskini: <?= date('d/m/Y H:i', strtotime($activeTemplate['tarikh_kemaskini'])); ?></span>
        </div>

        <form method="POST" action="?page=admin_email_templates_save">
            <?= csrfField(); ?>
            <input type="hidden" name="template_key" value="<?= htmlspecialchars($activeTemplate['template_key']); ?>">
            
            <div class="form-group">
                <label for="subject" style="font-weight: 600; font-size: 13px; margin-bottom: 8px;">Subjek Emel <span style="color: #ef4444;">*</span></label>
                <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($activeTemplate['subject']); ?>" required 
                       style="padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; width: 100%;">
            </div>
            
            <div class="form-group">
                <label for="content" style="font-weight: 600; font-size: 13px; margin-bottom: 8px;">Kandungan Emel (HTML Dibenarkan) <span style="color: #ef4444;">*</span></label>
                <textarea name="content" id="content" rows="16" required 
                          style="padding: 12px; border-radius: 8px; border: 1px solid var(--border); font-family: monospace; font-size: 13px; width: 100%; line-height: 1.5;"><?= htmlspecialchars($activeTemplate['content']); ?></textarea>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-teal" style="min-width: 150px; border-radius: 8px; padding: 10px 20px; height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                    Simpan Templat
                </button>
            </div>
        </form>
    </div>

    <!-- RUJUKAN PLACEHOLDER TOKENS -->
    <div class="card" style="align-self: flex-start;">
        <h3 style="color: #1e293b; font-size: 15px; font-weight: 600; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Token Rujukan</h3>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 15px; line-height: 1.4;">
            Salin dan tampal kod token di bawah ke dalam subjek atau kandungan emel untuk memaparkan maklumat permohonan secara dinamik:
        </p>

        <div class="token-badge">{nama_penjaga}</div>
        <div class="token-desc">Nama penuh bapa/ibu/penjaga utama pelajar.</div>

        <div class="token-badge">{nama_pelajar}</div>
        <div class="token-desc">Nama penuh pelajar yang didaftarkan.</div>

        <div class="token-badge">{no_rujukan}</div>
        <div class="token-desc">Nombor rujukan permohonan pendaftaran.</div>

        <div class="token-badge">{no_pelajar}</div>
        <div class="token-desc">Nombor Pelajar Rasmi yang dijana (hanya untuk templat permohonan diluluskan).</div>

        <div class="token-badge">{catatan}</div>
        <div class="token-desc">Catatan pembetulan atau sebab permohonan ditolak.</div>

        <div class="token-badge">{brand}</div>
        <div class="token-desc">Nama organisasi rasmi ("Tahfiz Ainuddin").</div>
    </div>
</div>

<script>
function switchTemplate(key) {
    window.location.href = '?page=admin_email_templates&key=' + encodeURIComponent(key);
}
</script>
