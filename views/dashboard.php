<?php
require_once "app/controllers/PermohonanController.php";
 $permCtrl = new PermohonanController();
 $applications = $permCtrl->getUserApplications($_SESSION['id_pengguna']);

 $total     = count($applications);
 $draft     = count(array_filter($applications, fn($a) => $a['kod_status'] === '00'));
 $submitted = count(array_filter($applications, fn($a) => $a['kod_status'] === '03'));
 $approved  = count(array_filter($applications, fn($a) => $a['kod_status'] === '04'));
 $rejected  = count(array_filter($applications, fn($a) => $a['kod_status'] === '05'));

// Check if user has active drafts/submissions
 $hasActive = count(array_filter($applications, fn($a) => in_array($a['kod_status'], ['00', '03']))) > 0;
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
                        <?php if ($app['kod_status'] == '00'): ?>
                            <div class="action-group">
                                <a href="?page=resume_permohonan&id=<?= $app['id_permohonan']; ?>" class="action-link">Sambung</a>
                                <form method="POST" action="?page=delete_permohonan" onsubmit="return confirm('Adakah anda pasti ingin memadam draf ini?');">
                                    <?= csrfField(); ?>
                                    <input type="hidden" name="id_permohonan" value="<?= $app['id_permohonan']; ?>">
                                    <button type="submit" class="action-link action-delete">Padam</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <span style="color: #cbd5e1; font-size: 13px;">-</span>
                        <?php endif; ?>
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