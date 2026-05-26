<?php
require_once "app/controllers/PermohonanController.php";
require_once "config/database.php";
 $permCtrl = new PermohonanController();
 $applications = $permCtrl->getUserApplications($_SESSION['id_pengguna']);

 $total     = count($applications);
 $draft     = count(array_filter($applications, fn($a) => $a['kod_status'] === '00'));
 $submitted = count(array_filter($applications, fn($a) => $a['kod_status'] === '03'));
 $approved  = count(array_filter($applications, fn($a) => $a['kod_status'] === '04'));
 $rejected  = count(array_filter($applications, fn($a) => $a['kod_status'] === '05'));

// Check if user has active drafts/submissions/revisions
 $hasActive = count(array_filter($applications, fn($a) => in_array($a['kod_status'], ['00', '03', '08']))) > 0;

// Check for revision-required permohonan to display alert
$revisionApps = array_filter($applications, fn($a) => $a['kod_status'] === '08');
$revisionAlert = null;
if (!empty($revisionApps)) {
    $revApp = reset($revisionApps);
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT ls.catatan, ls.tarikh, pg.nama_penuh as nama_admin
        FROM log_status ls
        LEFT JOIN pengguna pg ON ls.dikemaskini_oleh = pg.id_pengguna
        WHERE ls.id_permohonan = ? AND ls.kod_status = '08'
        ORDER BY ls.tarikh DESC LIMIT 1
    ");
    $stmt->execute([$revApp['id_permohonan']]);
    $revisionAlert = $stmt->fetch();
    if ($revisionAlert) {
        $revisionAlert['id_permohonan'] = $revApp['id_permohonan'];
    }
}
?>

<style>
    .student-header { background: white; border-radius: 12px; padding: 24px 28px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .student-header h2 { margin: 0; font-size: 22px; font-weight: 600; color: #1e293b; }
    .student-header .subtext { color: #64748b; font-size: 14px; margin-top: 4px; }
    .btn-permohonan { background: #1e5631; color: white; padding: 10px 22px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; transition: background 0.2s; border: none; cursor: pointer; font-family: inherit; }
    .btn-permohonan:hover { background: #163d26; }
    .btn-permohonan.disabled { background: #cbd5e1; cursor: not-allowed; }
    .btn-permohonan.disabled:hover { background: #cbd5e1; }
    .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 30px; }
    .stat-item { background: white; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; }
    .stat-item .stat-number { font-size: 26px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
    .stat-item .stat-label { font-size: 13px; color: #64748b; font-weight: 500; }
    .app-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; }
    .app-table th { background: #f8fafc; font-weight: 600; color: #475569; padding: 14px 16px; font-size: 13px; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .app-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    .app-table tr:last-child td { border-bottom: none; }
    .app-table tr:hover td { background: #f8fafc; }
    .badge { display: inline-block; padding: 3px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-draft { background: #fef3c7; color: #92400e; }
    .badge-submitted { background: #dbeafe; color: #1e40af; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-warning { background: #fef3c7; color: #d97706; }
    .action-link { background: #e0f2f1; color: #00796b; padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-block; border: none; cursor: pointer; font-family: inherit; }
    .action-link:hover { background: #b2dfdb; }
    .action-delete { background: #fee2e2; color: #b91c1c; }
    .action-delete:hover { background: #fecaca; }
    .action-group { display: flex; gap: 8px; align-items: center; }
    .empty-message { background: white; border-radius: 12px; padding: 60px 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; color: #64748b; }
</style>

<div class="student-header">
    <div>
        <h2>Selamat datang, <?= htmlspecialchars($_SESSION['nama_penuh']); ?></h2>
        <div class="subtext">Urus permohonan pendaftaran pelajar anda</div>
    </div>
    <?php if ($hasActive): ?>
        <button class="btn-permohonan disabled" disabled title="Anda mempunyai permohonan aktif. Sila lengkapkan atau padam sebelum membuat yang baru.">+ Permohonan Baru</button>
    <?php else: ?>
        <a href="?page=mula_permohonan" class="btn-permohonan">+ Permohonan Baru</a>
    <?php endif; ?>
</div>

<?php if ($revisionAlert): ?>
    <div class="alert alert-error" style="background: #fffbeb; border: 1px solid #fde68a; color: #b45309; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); font-family: inherit;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; color: #b45309;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Tindakan Diperlukan: Kemaskini Maklumat Permohonan
                </h4>
                <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #78350f;">
                    Pentadbir (<strong><?= htmlspecialchars($revisionAlert['nama_admin'] ?? 'Sistem'); ?></strong>) telah meminta pembetulan pada permohonan anda dengan catatan:
                </p>
                <div style="background: white; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; margin-top: 10px; font-style: italic; color: #78350f; font-weight: 500; font-size: 14px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                    "<?= nl2br(htmlspecialchars($revisionAlert['catatan'])); ?>"
                </div>
                <small style="display: block; margin-top: 8px; color: #b45309; opacity: 0.8; font-size: 11px;">
                    Diminta pada: <?= date('d/m/Y H:i', strtotime($revisionAlert['tarikh'])); ?>
                </small>
            </div>
            <div style="margin-top: 5px;">
                <a href="?page=resume_permohonan&id=<?= $revisionAlert['id_permohonan']; ?>" class="btn-permohonan" style="background: #d97706; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: white; border-radius: 8px; font-weight: 600; padding: 10px 22px;">
                    Mula Kemaskini
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="stat-row">
    <div class="stat-item"><div class="stat-number"><?= $total; ?></div><div class="stat-label">Jumlah</div></div>
    <div class="stat-item"><div class="stat-number"><?= $draft; ?></div><div class="stat-label">Draf</div></div>
    <div class="stat-item"><div class="stat-number"><?= $submitted; ?></div><div class="stat-label">Dihantar</div></div>
    <div class="stat-item"><div class="stat-number"><?= $approved; ?></div><div class="stat-label">Diluluskan</div></div>
    <div class="stat-item"><div class="stat-number"><?= $rejected; ?></div><div class="stat-label">Ditolak</div></div>
</div>

<h3 style="margin: 0 0 15px; font-size: 18px; color: #1e293b;">Senarai Permohonan</h3>

<?php if (!empty($applications)): ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>Nama Pelajar</th>
                <th>No. Rujukan</th>
                <th>Status</th>
                <th>Tarikh</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
                <?php
                $statusMap = [
                    '00' => ['Draf', 'badge-draft'],
                    '03' => ['Dihantar', 'badge-submitted'],
                    '04' => ['Diluluskan', 'badge-approved'],
                    '05' => ['Ditolak', 'badge-rejected'],
                    '08' => ['Perlu Kemaskini', 'badge-warning'],
                ];
                [$statusText, $badgeClass] = $statusMap[$app['kod_status']] ?? ['Draf', 'badge-draft'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($app['nama_pelajar'] ?? 'Tanpa Nama'); ?></td>
                    <td><?= htmlspecialchars($app['no_rujukan'] ?? '-'); ?></td>
                    <td><span class="badge <?= $badgeClass; ?>"><?= $statusText; ?></span></td>
                    <td>
                        <?= $app['tarikh_hantar'] 
                            ? date('d/m/Y', strtotime($app['tarikh_hantar'])) 
                            : date('d/m/Y', strtotime($app['tarikh_cipta'])); ?>
                    </td>
                    <td>
                        <div class="action-group">
                            <?php if ($app['kod_status'] == '00'): ?>
                                <a href="?page=resume_permohonan&id=<?= $app['id_permohonan']; ?>" class="action-link">Sambung</a>
                                <form method="POST" action="?page=delete_permohonan" onsubmit="return confirm('Adakah anda pasti ingin memadam draf ini?');">
                                    <?= csrfField(); ?>
                                    <input type="hidden" name="id_permohonan" value="<?= $app['id_permohonan']; ?>">
                                    <button type="submit" class="action-link action-delete">Padam</button>
                                </form>
                            <?php elseif ($app['kod_status'] == '08'): ?>
                                <a href="?page=resume_permohonan&id=<?= $app['id_permohonan']; ?>" class="action-link" style="background: #f59e0b; color: white;">Kemaskini</a>
                            <?php elseif ($app['kod_status'] == '04'): ?>
                                <a href="?page=cetak_surat_tawaran" target="_blank" class="action-link" style="background: var(--teal); color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Surat Tawaran</a>
                                <a href="?page=download_peraturan" target="_blank" class="action-link" style="background: #475569; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Surat Peraturan</a>
                            <?php else: ?>
                                <span style="color: #64748b; font-size: 13px;">Tiada Tindakan</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-message">
        <p>Belum ada permohonan. Klik "+ Permohonan Baru" untuk mula.</p>
    </div>
<?php endif; ?>