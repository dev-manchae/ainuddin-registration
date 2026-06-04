<?php

if (!$detail) {
    echo "<div class='alert alert-error'>Permohonan tidak ditemui.</div>";
    return;
}
?>
<style>
/* PREMIUM LIGHTBOX MODAL */
.lightbox-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(15, 23, 42, 0.6); /* Dark slate overlay */
    backdrop-filter: blur(8px); /* Modern blur effect */
    -webkit-backdrop-filter: blur(8px);
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.lightbox-modal.active {
    display: flex;
    opacity: 1;
}

.lightbox-content-wrapper {
    background: #ffffff;
    width: 90%;
    max-width: 950px;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transform: scale(0.95);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    display: flex;
    flex-direction: column;
}

.lightbox-modal.active .lightbox-content-wrapper {
    transform: scale(1);
}

.lightbox-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.lightbox-title {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 80%;
}

.lightbox-close-btn {
    background: none;
    border: none;
    font-size: 28px;
    font-weight: 400;
    color: #64748b;
    cursor: pointer;
    transition: color 0.2s;
    line-height: 1;
    padding: 0;
}

.lightbox-close-btn:hover {
    color: #0f172a;
}

.lightbox-body {
    padding: 24px;
    background: #f1f5f9;
    max-height: 70vh;
    overflow-y: auto;
}

.lightbox-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
}
</style>
<?php

 $p  = $detail['permohonan'];
 $pl = $detail['pelajar'];
 $kl = $detail['keluarga'];
 $ak = $detail['akademik'];
 $ks = $detail['kesihatan'];
 $dk = $detail['dokumen'];
 $log = $detail['logStatus'];

 $hasNoPelajar = !empty($pl['no_pelajar']);

// Decode surah_hafazan (backward compatible with old combined JSON format)
 $surahDecoded = null;
if (!empty($ak['surah_hafazan'])) {
    $surahDecoded = json_decode($ak['surah_hafazan'], true);
}
 $surahText = is_array($surahDecoded) ? ($surahDecoded['surah_hafazan'] ?? '-') : ($ak['surah_hafazan'] ?? '-');

// Load keputusan_agama from its new column, or fallback to the combined JSON in surah_hafazan
 $agamaResults = [];
if (isset($ak['keputusan_agama']) && !empty($ak['keputusan_agama'])) {
    $agamaResults = json_decode($ak['keputusan_agama'], true) ?: [];
} elseif (is_array($surahDecoded) && isset($surahDecoded['keputusan_agama'])) {
    $agamaResults = $surahDecoded['keputusan_agama'];
}
?>

<!-- HEADER -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
    <div>
        <h2>Lihat Permohonan</h2>
        <p style="color: #64748b; font-size: 14px;">No Rujukan: <strong><?= htmlspecialchars($p['no_rujukan'] ?? 'Draf'); ?></strong></p>
        <?php if ($hasNoPelajar): ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px 20px; border-radius: 8px; margin-top: 15px; display: inline-block;">
                <span style="color: #166534; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">No Pelajar Rasmi</span><br>
                <span style="font-size: 24px; font-weight: 700; color: #166534; letter-spacing: 1px;">
                    <?= htmlspecialchars($pl['no_pelajar']); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="?page=admin_cetak_profil&id=<?= $p['id_permohonan']; ?>" class="btn" style="background-color: #1e5631; color: white;" target="_blank">Cetak Profil PDF</a>
        <a href="?page=admin_senarai" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<br>

<!-- STATUS + ACTION -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <strong>Status Semasa:</strong>
            <?php
            $badgeClass = match($p['kod_status']) {
                '03' => 'badge-submitted',
                '04' => 'badge-approved',
                '05' => 'badge-rejected',
                '08' => 'badge-warning',
                default => 'badge-draft'
            };
            ?>
            <span class="badge <?= $badgeClass; ?>">
                <?= htmlspecialchars($p['status_perihal']); ?>
            </span>
            <?php if ($hasNoPelajar): ?>
                <span style="margin-left: 10px; color: #16a34a; font-size: 13px; font-weight: 600;">Telah diluluskan</span>
            <?php endif; ?>
        </div>

        <?php if ($p['kod_status'] == '03'): ?>
            <div style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                <form method="POST" action="?page=admin_update_status&id=<?= $p['id_permohonan']; ?>" style="display: flex; gap: 10px; align-items: flex-end;">
                    <?= csrfField(); ?>
                    <input type="hidden" name="kod_status" value="04">
                    <input type="hidden" name="catatan" value="Permohonan diluluskan. No Pelajar dijana secara automatik.">
                    <div class="form-group" style="margin:0;">
                        <label>Batch (2 digit)</label>
                        <input type="text" name="batch" pattern="\d{2}" maxlength="2" placeholder="01" required
                               style="width:80px; padding:8px;">
                    </div>
                    <button type="submit" class="btn btn-success">Luluskan</button>
                </form>
                <button onclick="showRejectForm()" class="btn btn-danger">Tolak</button>
                <button onclick="showRevisionForm()" class="btn" style="background: #d97706; color: white;">Minta Kemaskini</button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($p['kod_status'] == '04'): ?>
        <br>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; font-size: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <strong>Permohonan ini telah diluluskan.</strong>
                <?php if ($hasNoPelajar): ?>
                    <br>Pelajar telah diberikan No Pelajar rasmi: <strong><?= htmlspecialchars($pl['no_pelajar']); ?></strong>
                <?php else: ?>
                    <br>No Pelajar akan dijana setelah maklumat program disahkan.
                <?php endif; ?>
            </div>
            <div>
                <a href="?page=admin_cetak_surat_tawaran&id=<?= $p['id_permohonan']; ?>" target="_blank" class="btn btn-teal" style="background: var(--teal); color: white; border: none; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Cetak Surat Tawaran
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- REJECT FORM (hidden) -->
<div id="rejectForm" class="card" style="display: none;">
    <h3>Catatan Penolakan</h3><br>
    <form method="POST" action="?page=admin_update_status&id=<?= $p['id_permohonan']; ?>">
        <?= csrfField(); ?>
        <input type="hidden" name="kod_status" value="05">
        <div class="form-group">
            <label>Sebab Penolakan</label>
            <textarea name="catatan" required placeholder="Nyatakan sebab penolakan..." rows="4"></textarea>
        </div>
        <br>
        <button type="submit" class="btn btn-danger">Sahkan Tolak</button>
        <button type="button" onclick="hideRejectForm()" class="btn btn-secondary">Batal</button>
    </form>
</div>

<!-- REVISION FORM (hidden) -->
<div id="revisionForm" class="card" style="display: none; border-left: 4px solid #d97706;">
    <h3>Catatan Pembetulan / Kemaskini</h3><br>
    <form method="POST" action="?page=admin_update_status&id=<?= $p['id_permohonan']; ?>">
        <?= csrfField(); ?>
        <input type="hidden" name="kod_status" value="08">
        <div class="form-group">
            <label>Keterangan / Pembetulan yang Diperlukan</label>
            <textarea name="catatan" required placeholder="Sila nyatakan bahagian yang perlu diperbetulkan oleh pemohon (contoh: Sijil akademik kurang jelas, sila muat naik semula)..." rows="4"></textarea>
        </div>
        <br>
        <button type="submit" class="btn" style="background: #d97706; color: white;">Hantar Arahan Kemaskini</button>
        <button type="button" onclick="hideRevisionForm()" class="btn btn-secondary">Batal</button>
    </form>
</div>

<script>
function showRejectForm() {
    document.getElementById('rejectForm').style.display = 'block';
    document.getElementById('revisionForm').style.display = 'none';
}
function hideRejectForm() {
    document.getElementById('rejectForm').style.display = 'none';
}
function showRevisionForm() {
    document.getElementById('revisionForm').style.display = 'block';
    document.getElementById('rejectForm').style.display = 'none';
}
function hideRevisionForm() {
    document.getElementById('revisionForm').style.display = 'none';
}
</script>

<br>

<!-- TABS -->
<div class="tabs">
    <?php
    $tabs = [1 => 'Pelajar', 2 => 'Penjaga', 3 => 'Akademik', 4 => 'Kesihatan', 5 => 'Dokumen', 6 => 'Log Status'];
    foreach ($tabs as $num => $label):
    ?>
        <a href="javascript:void(0);"
           class="admin-tab-btn <?= ($num == 1) ? 'active' : ''; ?>"
           onclick="switchAdminTab(<?= $num; ?>)"
           data-tab="<?= $num; ?>">
            <?= $label; ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- TAB CONTENT -->
<div class="card" style="position: relative;">
    <!-- Tab 1: Pelajar -->
    <div id="admin-tab-content-1" class="admin-tab-panel" style="display: block;">
        <h3>Maklumat Pelajar</h3><br>
        <?php if ($pl): ?>
            <div class="detail-grid">
                <div class="detail-label">Nama Penuh</div><div class="detail-value"><?= htmlspecialchars($pl['nama_penuh'] ?? '-'); ?></div>
                <div class="detail-label">No KP</div><div class="detail-value"><?= htmlspecialchars($pl['no_kp'] ?? '-'); ?></div>
                <div class="detail-label">Jantina</div><div class="detail-value"><?= htmlspecialchars($pl['jantina'] ?? '-'); ?></div>
                <div class="detail-label">Tarikh Lahir</div><div class="detail-value"><?= htmlspecialchars($pl['tarikh_lahir'] ?? '-'); ?></div>
                <div class="detail-label">Tempat Lahir</div><div class="detail-value"><?= htmlspecialchars($pl['tempat_lahir'] ?? '-'); ?></div>
                <div class="detail-label">Warganegara</div><div class="detail-value"><?= htmlspecialchars($pl['warganegara'] ?? '-'); ?></div>
                <div class="detail-label">Alamat</div><div class="detail-value"><?= nl2br(htmlspecialchars($pl['alamat'] ?? '-')); ?></div>
                <div class="detail-label">Negeri</div><div class="detail-value"><?= htmlspecialchars($pl['negeri'] ?? '-'); ?></div>
                <div class="detail-label">Cawangan</div><div class="detail-value"><?= htmlspecialchars($pl['cawangan'] ?? '-'); ?></div>
                <div class="detail-label">Program</div><div class="detail-value"><?= htmlspecialchars($pl['program'] ?? '-'); ?></div>
            </div>
        <?php else: ?>
            <p style="color: #64748b;">Tiada maklumat pelajar.</p>
        <?php endif; ?>
    </div>

    <!-- Tab 2: Penjaga -->
    <div id="admin-tab-content-2" class="admin-tab-panel" style="display: none;">
        <h3>Maklumat Penjaga</h3><br>
        <?php if (!empty($kl)): ?>
            <?php foreach ($kl as $penjaga): ?>
                <div style="border:1px solid #e2e8f0; padding:20px; border-radius:8px; margin-bottom:20px;">
                    <h4 style="margin-bottom: 15px; color: #334155;">Maklumat <?= htmlspecialchars($penjaga['jenis_penjaga'] ?? 'Penjaga'); ?></h4>
                    <div class="detail-grid">
                        <div class="detail-label">Nama</div><div class="detail-value"><?= htmlspecialchars($penjaga['nama_penuh'] ?? '-'); ?></div>
                        <div class="detail-label">Telefon</div><div class="detail-value"><?= htmlspecialchars($penjaga['no_telefon'] ?? '-'); ?></div>
                        <div class="detail-label">Emel</div><div class="detail-value"><?= htmlspecialchars($penjaga['emel'] ?? '-'); ?></div>
                        <div class="detail-label">Pekerjaan</div><div class="detail-value"><?= htmlspecialchars($penjaga['pekerjaan'] ?? '-'); ?></div>
                        <div class="detail-label">Pendapatan</div><div class="detail-value">RM <?= number_format($penjaga['pendapatan'] ?? 0, 2); ?></div>
                        <div class="detail-label">Alamat</div><div class="detail-value"><?= nl2br(htmlspecialchars($penjaga['alamat'] ?? '-')); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #64748b;">Tiada maklumat penjaga.</p>
        <?php endif; ?>
    </div>

    <!-- Tab 3: Akademik -->
    <div id="admin-tab-content-3" class="admin-tab-panel" style="display: none;">
        <h3>Maklumat Akademik</h3><br>
        <?php if ($ak): ?>
            <div class="detail-grid">
                <div class="detail-label">Sekolah</div><div class="detail-value"><?= htmlspecialchars($ak['nama_sekolah'] ?? '-'); ?></div>
                <div class="detail-label">Tahap Quran</div><div class="detail-value"><?= htmlspecialchars($ak['tahap_quran'] ?? '-'); ?></div>
                <div class="detail-label">Status Khatam</div><div class="detail-value"><?= htmlspecialchars($ak['status_khatam'] ?? '-'); ?></div>
                <div class="detail-label">Surah Hafazan</div><div class="detail-value"><?= nl2br(htmlspecialchars($surahText)); ?></div>
                <div class="detail-label">Keputusan Akademik</div>
                <div class="detail-value">
                    <?php
                    $akademikResults = json_decode($ak['keputusan_akademik'], true) ?: [];
                    if (!empty($akademikResults)):
                    ?>
                        <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:6px 10px; border:1px solid #e2e8f0; background:#f8fafc; font-size:13px;">Subjek</th>
                                    <th style="text-align:left; padding:6px 10px; border:1px solid #e2e8f0; background:#f8fafc; font-size:13px;">Keputusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($akademikResults as $item): ?>
                                    <tr>
                                        <td style="padding:6px 10px; border:1px solid #e2e8f0;"><?= htmlspecialchars($item['subjek'] ?? ''); ?></td>
                                        <td style="padding:6px 10px; border:1px solid #e2e8f0;"><?= htmlspecialchars($item['keputusan'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
                <div class="detail-label">Keputusan Sekolah Agama</div>
                <div class="detail-value">
                    <?php if (!empty($agamaResults)): ?>
                        <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:6px 10px; border:1px solid #e2e8f0; background:#f8fafc; font-size:13px;">Subjek</th>
                                    <th style="text-align:left; padding:6px 10px; border:1px solid #e2e8f0; background:#f8fafc; font-size:13px;">Keputusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agamaResults as $item): ?>
                                    <tr>
                                        <td style="padding:6px 10px; border:1px solid #e2e8f0;"><?= htmlspecialchars($item['subjek'] ?? ''); ?></td>
                                        <td style="padding:6px 10px; border:1px solid #e2e8f0;"><?= htmlspecialchars($item['keputusan'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p style="color: #64748b;">Tiada maklumat akademik.</p>
        <?php endif; ?>
    </div>

    <!-- Tab 4: Kesihatan -->
    <div id="admin-tab-content-4" class="admin-tab-panel" style="display: none;">
        <h3>Maklumat Kesihatan</h3><br>
        <?php if ($ks): ?>
            <div class="detail-grid">
                <div class="detail-label">Alahan</div><div class="detail-value"><?= nl2br(htmlspecialchars($ks['alahan'] ?? '-')); ?></div>
                <div class="detail-label">Penyakit Kronik</div><div class="detail-value"><?= nl2br(htmlspecialchars($ks['penyakit_kronik'] ?? '-')); ?></div>
                <div class="detail-label">Pengambilan Ubat</div><div class="detail-value"><?= nl2br(htmlspecialchars($ks['pengambilan_ubat'] ?? '-')); ?></div>
                <div class="detail-label">No Kecemasan</div><div class="detail-value"><?= htmlspecialchars($ks['nombor_kecemasan'] ?? '-'); ?></div>
                <div class="detail-label">Kebenaran Rawatan</div><div class="detail-value"><?= ($ks['kebenaran_rawatan'] ?? '') == 'Ya' ? 'Ya' : 'Tidak'; ?></div>
            </div>
        <?php else: ?>
            <p style="color: #64748b;">Tiada maklumat kesihatan.</p>
        <?php endif; ?>
    </div>

    <!-- Tab 5: Dokumen -->
    <div id="admin-tab-content-5" class="admin-tab-panel" style="display: none;">
        <h3>Dokumen Dimuat Naik</h3><br>
        <?php
        $docTypes = ['IC Pelajar' => 'IC Pelajar', 'Gambar Pelajar' => 'Gambar Pelajar', 'Sijil Pelajar' => 'Sijil Pelajar'];
        if (!empty($dk)):
            foreach ($docTypes as $key => $label):
                ?>
                <div class="doc-wrapper-card" style="border:1px solid #e2e8f0; padding:20px; border-radius:8px; margin-bottom:20px;">
                    <h4 style="margin-bottom: 15px; color: #334155;"><?= $label; ?></h4>
                    <?php if (!empty($dk[$key])): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($dk[$key] as $doc):
                                $filePath = '';
                                if ($key == 'IC Pelajar') $filePath = 'public/uploads/pelajar_ic/' . $doc['nama_fail'];
                                elseif ($key == 'Gambar Pelajar') $filePath = 'public/uploads/gambar/' . $doc['nama_fail'];
                                elseif ($key == 'Sijil Pelajar') $filePath = 'public/uploads/sijil/' . $doc['nama_fail'];
                                
                                $extension = strtolower(pathinfo($doc['nama_asal'], PATHINFO_EXTENSION));
                                ?>
                                <div style="border:1px solid #f1f5f9; padding:15px; border-radius:6px; background-color:#fafafa;">
                                    <div class="detail-grid">
                                        <div class="detail-label">Nama Fail</div><div class="detail-value"><?= htmlspecialchars($doc['nama_asal']); ?></div>
                                        <div class="detail-label">Tarikh Upload</div><div class="detail-value"><?= date('d/m/Y H:i', strtotime($doc['tarikh_upload'])); ?></div>
                                        <div class="detail-label">Pratonton</div>
                                        <div class="detail-value">
                                            <?php if (in_array($extension, ['jpg','jpeg','png'])): ?>
                                                <a href="<?= $filePath; ?>" target="_blank" class="img-preview-anchor">
                                                    <img src="<?= $filePath; ?>" class="img-preview-direct" alt="<?= $label; ?>">
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= $filePath; ?>" target="_blank" class="doc-preview-card">
                                                    <div class="doc-preview-icon pdf">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                    </div>
                                                    <div class="doc-preview-info">
                                                        <span class="doc-preview-name" title="<?= htmlspecialchars($doc['nama_asal']); ?>"><?= htmlspecialchars($doc['nama_asal']); ?></span>
                                                        <span class="doc-preview-action">Klik untuk buka PDF</span>
                                                    </div>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #64748b;">Tiada dokumen.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach;
        else: ?>
            <p style="color: #64748b;">Tiada dokumen dimuat naik.</p>
        <?php endif; ?>
    </div>

    <!-- Tab 6: Log Status -->
    <div id="admin-tab-content-6" class="admin-tab-panel" style="display: none;">
        <h3>Log Status</h3><br>
        <?php if (!empty($log)): ?>
            <table>
                <thead><tr><th>Tarikh</th><th>Status</th><th>Dikemaskini Oleh</th><th>Catatan</th></tr></thead>
                <tbody>
                <?php foreach ($log as $entry): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($entry['tarikh'])); ?></td>
                        <td>
                            <?php
                            $logBadge = match($entry['kod_status']) {
                                '03' => 'badge-submitted',
                                '04' => 'badge-approved',
                                '05' => 'badge-rejected',
                                default => 'badge-draft'
                            };
                            ?>
                            <span class="badge <?= $logBadge; ?>"><?= htmlspecialchars($entry['status_perihal']); ?></span>
                        </td>
                        <td><?= htmlspecialchars($entry['nama_admin'] ?? 'Sistem'); ?></td>
                        <td><?= htmlspecialchars($entry['catatan'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #64748b;">Tiada log status.</p>
        <?php endif; ?>
    </div>
</div>

<!-- LIGHTBOX MODAL CONTAINER -->
<div id="previewLightbox" class="lightbox-modal">
    <div class="lightbox-content-wrapper">
        <div class="lightbox-header">
            <span id="lightboxTitle" class="lightbox-title">Fail Dokumen</span>
            <button onclick="closeLightbox()" class="lightbox-close-btn" aria-label="Tutup">&times;</button>
        </div>
        <div class="lightbox-body">
            <div id="lightboxImageContainer" style="display: none; height: 100%; align-items: center; justify-content: center;">
                <img id="lightboxImage" src="" alt="Pratonton Imej" style="max-width: 100%; max-height: 60vh; object-fit: contain;">
            </div>
            <div id="lightboxPdfContainer" style="display: none; height: 100%;">
                <iframe id="lightboxPdfFrame" src="" style="width: 100%; height: 60vh; border: none; border-radius: 8px;"></iframe>
            </div>
        </div>
        <div class="lightbox-footer">
            <a id="lightboxDownloadBtn" href="" target="_blank" class="btn btn-teal">Buka di Tab Baru</a>
            <button onclick="closeLightbox()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>

<script>
function switchAdminTab(tabNum) {
    const panels = document.querySelectorAll('.admin-tab-panel');
    panels.forEach(p => p.style.display = 'none');
    
    const activePanel = document.getElementById('admin-tab-content-' + tabNum);
    if (activePanel) {
        activePanel.style.display = 'block';
    }
    
    const tabBtns = document.querySelectorAll('.admin-tab-btn');
    tabBtns.forEach(btn => {
        if (btn.getAttribute('data-tab') == tabNum) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

function openLightbox(title, filePath, isImage) {
    const modal = document.getElementById('previewLightbox');
    const titleEl = document.getElementById('lightboxTitle');
    const imgContainer = document.getElementById('lightboxImageContainer');
    const pdfContainer = document.getElementById('lightboxPdfContainer');
    const imgEl = document.getElementById('lightboxImage');
    const pdfFrame = document.getElementById('lightboxPdfFrame');
    const downloadBtn = document.getElementById('lightboxDownloadBtn');
    
    titleEl.textContent = title;
    downloadBtn.href = filePath;
    
    if (isImage) {
        imgEl.src = filePath;
        imgContainer.style.display = 'flex';
        pdfContainer.style.display = 'none';
        pdfFrame.src = ''; // Clear iframe src
    } else {
        pdfFrame.src = filePath;
        pdfContainer.style.display = 'block';
        imgContainer.style.display = 'none';
        imgEl.src = ''; // Clear image src
    }
    
    modal.style.display = 'flex';
    // Force browser reflow to trigger transition
    modal.offsetHeight;
    modal.classList.add('active');
    
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('previewLightbox');
    modal.classList.remove('active');
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.getElementById('lightboxImage').src = '';
        document.getElementById('lightboxPdfFrame').src = '';
        document.body.style.overflow = '';
    }, 300);
}

// Close lightbox on clicking backdrop
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('previewLightbox');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeLightbox();
            }
        });
    }
    
    // ESC key close support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('previewLightbox');
            if (modal && modal.classList.contains('active')) {
                closeLightbox();
            }
        }
    });

    // Intercept clicks on preview links inside tab 5
    const imgAnchors = document.querySelectorAll('.img-preview-anchor');
    imgAnchors.forEach(function(anchor) {
        anchor.removeAttribute('target');
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const filePath = this.getAttribute('href');
            const parentCard = this.closest('.doc-wrapper-card');
            const headerEl = parentCard ? parentCard.querySelector('h4') : null;
            const labelText = headerEl ? headerEl.textContent.trim() : 'Imej';
            const detailGrid = this.closest('.detail-grid');
            const nameEl = detailGrid ? detailGrid.querySelector('.detail-value') : null;
            const fileName = nameEl ? nameEl.textContent.trim() : 'Pratonton Imej';
            openLightbox(labelText + ': ' + fileName.split('\n')[0], filePath, true);
        });
    });

    const docCards = document.querySelectorAll('.doc-preview-card');
    docCards.forEach(function(cardLink) {
        cardLink.removeAttribute('target');
        cardLink.addEventListener('click', function(e) {
            e.preventDefault();
            const filePath = this.getAttribute('href');
            const parentCard = this.closest('.doc-wrapper-card');
            const headerEl = parentCard ? parentCard.querySelector('h4') : null;
            const labelText = headerEl ? headerEl.textContent.trim() : 'Dokumen';
            const fileName = this.querySelector('.doc-preview-name') ? this.querySelector('.doc-preview-name').textContent.trim() : 'Pratonton Fail';
            openLightbox(labelText + ': ' + fileName, filePath, false);
        });
    });
});
</script>