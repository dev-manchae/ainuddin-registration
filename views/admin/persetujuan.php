<?php
// views/admin/persetujuan.php
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="margin-bottom: 5px;">Urus Persetujuan</h2>
        <p style="color: #64748b; font-size: 14px;">Urus senarai klausa persetujuan/peraturan yang perlu dipersetujui oleh ibu bapa/penjaga pada Langkah 6 pendaftaran.</p>
    </div>
</div>

<style>
.persetujuan-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 992px) {
    .persetujuan-container {
        grid-template-columns: 1fr;
    }
}
.perihal-text {
    font-size: 14px;
    color: #334155;
    line-height: 1.5;
    word-break: break-word;
}
</style>

<div class="persetujuan-container">
    <!-- LEFT COLUMN: AGREEMENTS LIST -->
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Senarai Persetujuan Aktif & Tidak Aktif</h3>
        
        <?php if (empty($agreements)): ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <p>Tiada klausa persetujuan ditemui. Sila tambah klausa baru menggunakan borang di sebelah.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No.</th>
                            <th>Klausa Persetujuan</th>
                            <th style="width: 120px; text-align: center;">Status</th>
                            <th style="width: 220px; text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $bil = 1; foreach ($agreements as $agreement): ?>
                            <tr>
                                <td style="text-align: center; color: #64748b; font-size: 13px;">
                                    <?= $bil++; ?>
                                </td>
                                <td>
                                    <div class="perihal-text"><?= htmlspecialchars($agreement['perihal']); ?></div>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($agreement['status'] === 'Y'): ?>
                                        <span class="badge badge-approved">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-rejected">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                        <button type="button" 
                                                onclick="editAgreement(<?= $agreement['id_persetujuan']; ?>, <?= htmlspecialchars(json_encode($agreement['perihal']), ENT_QUOTES, 'UTF-8'); ?>)" 
                                                class="btn btn-primary" 
                                                style="padding: 6px 12px; font-size: 12px; border-radius: 6px;">
                                            Edit
                                        </button>
                                        <a href="?page=admin_persetujuan_toggle&id=<?= $agreement['id_persetujuan']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" 
                                           class="btn btn-teal" 
                                           style="padding: 6px 12px; font-size: 12px; border-radius: 6px;">
                                            Tukar Status
                                        </a>
                                        <a href="?page=admin_persetujuan_delete&id=<?= $agreement['id_persetujuan']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" 
                                           class="btn btn-danger" 
                                           style="padding: 6px 12px; font-size: 12px; border-radius: 6px;" 
                                           onclick="return confirm('Adakah anda pasti mahu memadam klausa persetujuan ini?');">
                                            Padam
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT COLUMN: ADD / EDIT FORM -->
    <div class="card" id="formCard">
        <h3 id="formTitle" style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Tambah Persetujuan Baru</h3>
        
        <form id="agreementForm" method="POST" action="?page=admin_persetujuan_save">
            <?= csrfField(); ?>
            <input type="hidden" name="id_persetujuan" id="id_persetujuan" value="">
            
            <div class="form-group">
                <label for="perihal" style="margin-bottom: 8px;">Perihal / Klausa Persetujuan <span style="color: #ef4444;">*</span></label>
                <textarea name="perihal" id="perihal" rows="6" placeholder="Masukkan ayat klausa persetujuan di sini..." required></textarea>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                <button type="submit" id="submitBtn" class="btn btn-teal" style="width: 100%; border-radius: 8px; padding: 10px;">
                    Tambah Klausa
                </button>
                <button type="button" id="cancelBtn" onclick="resetForm()" class="btn btn-secondary" style="width: 100%; border-radius: 8px; padding: 10px; display: none;">
                    Batal Edit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editAgreement(id, text) {
    document.getElementById('formTitle').textContent = 'Kemaskini Persetujuan (ID: ' + id + ')';
    document.getElementById('id_persetujuan').value = id;
    document.getElementById('perihal').value = text;
    document.getElementById('submitBtn').textContent = 'Kemaskini Klausa';
    document.getElementById('cancelBtn').style.display = 'block';
    
    // Focus and scroll to form on mobile
    document.getElementById('perihal').focus();
    document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Persetujuan Baru';
    document.getElementById('id_persetujuan').value = '';
    document.getElementById('perihal').value = '';
    document.getElementById('submitBtn').textContent = 'Tambah Klausa';
    document.getElementById('cancelBtn').style.display = 'none';
}
</script>
