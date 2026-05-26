<?php

if (!$detail) {
    echo "<div class='alert alert-error'>Permohonan tidak ditemui.</div>";
    return;
}

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
    <div>
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
    $currentTab = $_GET['tab'] ?? '1';
    foreach ($tabs as $num => $label):
    ?>
        <a href="?page=admin_lihat&id=<?= $p['id_permohonan']; ?>&tab=<?= $num; ?>"
           class="<?= ($currentTab == $num) ? 'active' : ''; ?>">
            <?= $label; ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- TAB CONTENT -->
<div class="card">
    <?php if ($currentTab == '1'): ?>
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

    <?php elseif ($currentTab == '2'): ?>
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

    <?php elseif ($currentTab == '3'): ?>
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

    <?php elseif ($currentTab == '4'): ?>
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

    <?php elseif ($currentTab == '5'): ?>
        <h3>Dokumen Dimuat Naik</h3><br>
        <?php
        $docTypes = ['IC Pelajar' => 'IC Pelajar', 'Gambar Pelajar' => 'Gambar Pelajar', 'Sijil Pelajar' => 'Sijil Pelajar'];
        if (!empty($dk)):
            foreach ($docTypes as $key => $label):
                ?>
                <div style="border:1px solid #e2e8f0; padding:20px; border-radius:8px; margin-bottom:20px;">
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

    <?php elseif ($currentTab == '6'): ?>
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
    <?php endif; ?>
</div>