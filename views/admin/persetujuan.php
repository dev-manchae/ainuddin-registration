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
.perihal-text {
    font-size: 14px;
    color: #334155;
    line-height: 1.5;
    word-break: break-word;
}
.persetujuan-form-layout {
    display: flex;
    gap: 20px;
    align-items: flex-end;
    flex-wrap: wrap;
}
.persetujuan-form-main {
    flex: 1;
    min-width: 300px;
}
.persetujuan-form-actions {
    display: flex;
    gap: 10px;
    min-width: 250px;
}
@media (max-width: 640px) {
    .persetujuan-form-actions {
        flex-direction: column;
        width: 100%;
    }
    .persetujuan-form-actions .btn {
        width: 100%;
    }
}
</style>

<!-- TAMBAH / EDIT FORM CARD -->
<div class="card" id="formCard" style="margin-bottom: 25px;">
    <h3 id="formTitle" style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Tambah Persetujuan Baru</h3>
    
    <form id="agreementForm" method="POST" action="?page=admin_persetujuan_save">
        <?= csrfField(); ?>
        <input type="hidden" name="id_persetujuan" id="id_persetujuan" value="">
        
        <div class="persetujuan-form-layout">
            <div class="form-group persetujuan-form-main" style="margin-bottom: 0;">
                <label for="perihal" style="margin-bottom: 8px;">Perihal / Klausa Persetujuan <span style="color: #ef4444;">*</span></label>
                <textarea name="perihal" id="perihal" rows="3" placeholder="Masukkan ayat klausa persetujuan di sini..." required style="padding: 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; width: 100%; resize: vertical;"></textarea>
            </div>
            
            <div class="persetujuan-form-actions">
                <button type="submit" id="submitBtn" class="btn btn-teal" style="flex: 1; border-radius: 8px; padding: 10px 20px; height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                    Tambah Klausa
                </button>
                <button type="button" id="cancelBtn" onclick="resetForm()" class="btn btn-secondary" style="flex: 1; border-radius: 8px; padding: 10px 20px; height: 44px; display: none; align-items: center; justify-content: center;">
                    Batal Edit
                </button>
            </div>
        </div>
    </form>
</div>

<!-- LIST CARD -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Senarai Persetujuan Aktif & Tidak Aktif</h3>
    
    <?php if (empty($agreements)): ?>
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p>Tiada klausa persetujuan ditemui. Sila tambah klausa baru menggunakan borang di atas.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 750px;">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">No.</th>
                        <th>Klausa Persetujuan</th>
                        <th style="width: 120px; text-align: center;">Status</th>
                        <th style="width: 280px; text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $bil = 1; foreach ($agreements as $agreement): ?>
                        <tr>
                            <td style="text-align: center; color: #64748b; font-size: 13px; vertical-align: middle;">
                                <?= $bil++; ?>
                            </td>
                            <td style="vertical-align: middle; padding-top: 15px; padding-bottom: 15px;">
                                <div class="perihal-text"><?= htmlspecialchars($agreement['perihal']); ?></div>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <?php if ($agreement['status'] === 'Y'): ?>
                                    <span class="badge badge-approved">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-rejected">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button type="button" 
                                            onclick="editAgreement(<?= $agreement['id_persetujuan']; ?>, <?= htmlspecialchars(json_encode($agreement['perihal']), ENT_QUOTES, 'UTF-8'); ?>)" 
                                            class="btn btn-primary" 
                                            style="padding: 6px 14px; font-size: 12px; border-radius: 6px;">
                                        Edit
                                    </button>
                                    <a href="?page=admin_persetujuan_toggle&id=<?= $agreement['id_persetujuan']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" 
                                       class="btn btn-teal" 
                                       style="padding: 6px 14px; font-size: 12px; border-radius: 6px; background: var(--teal); color: white;">
                                        Tukar Status
                                    </a>
                                    <a href="?page=admin_persetujuan_delete&id=<?= $agreement['id_persetujuan']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 6px 14px; font-size: 12px; border-radius: 6px;" 
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

<script>
function editAgreement(id, text) {
    document.getElementById('formTitle').textContent = 'Kemaskini Persetujuan (ID: ' + id + ')';
    document.getElementById('id_persetujuan').value = id;
    document.getElementById('perihal').value = text;
    document.getElementById('submitBtn').textContent = 'Kemaskini Klausa';
    
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.style.display = 'inline-flex';
    
    // Focus and scroll to form on mobile/desktop smoothly
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
