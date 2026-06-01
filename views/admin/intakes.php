<?php
// views/admin/intakes.php
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="margin-bottom: 5px;">Urus Sesi Pendaftaran (Intake)</h2>
        <p style="color: #64748b; font-size: 14px;">Urus tempoh masa, tarikh buka/tutup, dan had kuota pendaftaran bagi setiap sesi pengambilan pelajar.</p>
    </div>
</div>

<style>
.intake-form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 15px;
    align-items: flex-end;
}
.intake-form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    justify-content: flex-end;
}
@media (max-width: 991px) {
    .intake-form-grid {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 640px) {
    .intake-form-grid {
        grid-template-columns: 1fr;
    }
    .intake-form-actions {
        flex-direction: column;
    }
    .intake-form-actions .btn {
        width: 100%;
    }
}
</style>

<!-- TAMBAH / EDIT FORM CARD -->
<div class="card" id="formCard" style="margin-bottom: 25px;">
    <h3 id="formTitle" style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Tambah Sesi Pendaftaran Baru</h3>
    
    <form id="intakeForm" method="POST" action="?page=admin_intake_save">
        <?= csrfField(); ?>
        <input type="hidden" name="id_intake" id="id_intake" value="">
        
        <div class="intake-form-grid">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="nama_intake" style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">Nama Sesi Intake <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_intake" id="nama_intake" placeholder="Contoh: Sesi Akademik 2026/2027" required style="padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="tarikh_buka" style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">Tarikh Mula Buka <span style="color: #ef4444;">*</span></label>
                <input type="date" name="tarikh_buka" id="tarikh_buka" required style="padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="tarikh_tutup" style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">Tarikh Tutup <span style="color: #ef4444;">*</span></label>
                <input type="date" name="tarikh_tutup" id="tarikh_tutup" required style="padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="had_pelajar" style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">Had Quota Permohonan <span style="font-weight:normal; color:#64748b;">(0 = Tiada Had)</span></label>
                <input type="number" name="had_pelajar" id="had_pelajar" value="0" min="0" required style="padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; width: 100%;">
            </div>
        </div>
        
        <div class="intake-form-actions">
            <button type="submit" id="submitBtn" class="btn btn-teal" style="min-width: 150px; border-radius: 8px; padding: 10px 20px; height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                Tambah Sesi
            </button>
            <button type="button" id="cancelBtn" onclick="resetForm()" class="btn btn-secondary" style="min-width: 150px; border-radius: 8px; padding: 10px 20px; height: 44px; display: none; align-items: center; justify-content: center;">
                Batal Edit
            </button>
        </div>
    </form>
</div>

<!-- LIST CARD -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Senarai Sesi Pengambilan (Intake)</h3>
    
    <?php if (empty($intakes)): ?>
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p>Tiada sesi pendaftaran ditemui. Sila tambah sesi baru menggunakan borang di atas.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">No.</th>
                        <th>Sesi Pendaftaran (Intake)</th>
                        <th style="width: 130px; text-align: center;">Tarikh Mula</th>
                        <th style="width: 130px; text-align: center;">Tarikh Tutup</th>
                        <th style="width: 130px; text-align: center;">Kuota Limit</th>
                        <th style="width: 130px; text-align: center;">Pendaftaran</th>
                        <th style="width: 120px; text-align: center;">Status</th>
                        <th style="width: 280px; text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $bil = 1; foreach ($intakes as $intake): 
                        $bukaStr = date('d/m/Y', strtotime($intake['tarikh_buka']));
                        $tutupStr = date('d/m/Y', strtotime($intake['tarikh_tutup']));
                        $isExpired = (strtotime(date('Y-m-d')) > strtotime($intake['tarikh_tutup']));
                        $isUpcoming = (strtotime(date('Y-m-d')) < strtotime($intake['tarikh_buka']));
                    ?>
                        <tr>
                            <td style="text-align: center; color: #64748b; font-size: 13px; vertical-align: middle;">
                                <?= $bil++; ?>
                            </td>
                            <td style="vertical-align: middle; font-weight: 600; color: #334155; padding-top: 15px; padding-bottom: 15px;">
                                <?= htmlspecialchars($intake['nama_intake']); ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 13px; color: #475569;">
                                <?= $bukaStr; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 13px; color: #475569;">
                                <?= $tutupStr; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 13px; color: #475569;">
                                <?= $intake['had_pelajar'] > 0 ? (int)$intake['had_pelajar'] . ' orang' : 'Tiada Had'; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 13px; font-weight: 600; color: #1e293b;">
                                <?= (int)$intake['total_permohonan']; ?> orang
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <?php if ($intake['status'] === 'Y'): ?>
                                    <?php if ($isExpired): ?>
                                        <span class="badge badge-rejected" style="background: #e2e8f0; color: #64748b;">Tutup (Tamat)</span>
                                    <?php elseif ($isUpcoming): ?>
                                        <span class="badge badge-warning" style="background: #dbeafe; color: #1e40af;">Belum Mula</span>
                                    <?php else: ?>
                                        <span class="badge badge-approved">Dibuka</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-rejected">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center; white-space: nowrap;">
                                    <button type="button" 
                                            onclick="editIntake(<?= $intake['id_intake']; ?>, <?= htmlspecialchars(json_encode($intake['nama_intake']), ENT_QUOTES, 'UTF-8'); ?>, '<?= $intake['tarikh_buka']; ?>', '<?= $intake['tarikh_tutup']; ?>', <?= (int)$intake['had_pelajar']; ?>)" 
                                            class="btn btn-primary" 
                                            style="padding: 6px 14px; font-size: 12px; border-radius: 6px; white-space: nowrap;">
                                        Edit
                                    </button>
                                    <a href="?page=admin_intake_toggle&id=<?= $intake['id_intake']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" 
                                       class="btn btn-teal" 
                                       style="padding: 6px 14px; font-size: 12px; border-radius: 6px; background: var(--teal); color: white; white-space: nowrap;">
                                        Tukar Status
                                    </a>
                                    <a href="?page=admin_intake_delete&id=<?= $intake['id_intake']; ?>&csrf_token=<?= $_SESSION['csrf_token']; ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 6px 14px; font-size: 12px; border-radius: 6px; white-space: nowrap;" 
                                       onclick="return confirm('Adakah anda pasti mahu memadam sesi pendaftaran ini?');">
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
function editIntake(id, name, buka, tutup, limit) {
    document.getElementById('formTitle').textContent = 'Kemaskini Sesi Pendaftaran (ID: ' + id + ')';
    document.getElementById('id_intake').value = id;
    document.getElementById('nama_intake').value = name;
    document.getElementById('tarikh_buka').value = buka;
    document.getElementById('tarikh_tutup').value = tutup;
    document.getElementById('had_pelajar').value = limit;
    document.getElementById('submitBtn').textContent = 'Kemaskini Sesi';
    
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.style.display = 'inline-flex';
    
    // Focus and scroll to form smoothly
    document.getElementById('nama_intake').focus();
    document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Sesi Pendaftaran Baru';
    document.getElementById('id_intake').value = '';
    document.getElementById('nama_intake').value = '';
    document.getElementById('tarikh_buka').value = '';
    document.getElementById('tarikh_tutup').value = '';
    document.getElementById('had_pelajar').value = '0';
    document.getElementById('submitBtn').textContent = 'Tambah Sesi';
    document.getElementById('cancelBtn').style.display = 'none';
}
</script>
