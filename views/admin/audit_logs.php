<?php
// views/admin/audit_logs.php
$search = trim($_GET['search'] ?? '');
$logs = $adminController->getAuditLogs($search);
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="margin-bottom: 5px;">Log Audit Aktiviti Pentadbir</h2>
        <p style="color: #64748b; font-size: 14px;">Log jejak audit bagi semua tindakan keselamatan dan pengurusan yang dilakukan oleh pentadbir sistem.</p>
    </div>
    
    <!-- Search Form -->
    <form method="GET" action="" style="display: flex; gap: 10px; align-items: center; width: 100%; max-width: 350px;">
        <input type="hidden" name="page" value="admin_audit_logs">
        <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari emel, tindakan..." style="padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; flex-grow: 1; min-width: 0; background: white;">
        <button type="submit" class="btn btn-teal" style="border-radius: 8px; padding: 10px 18px; font-size: 14px; white-space: nowrap; height: 42px; display: inline-flex; align-items: center; justify-content: center;">Cari</button>
        <?php if (!empty($search)): ?>
            <a href="?page=admin_audit_logs" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 14px; font-size: 14px; height: 42px; display: inline-flex; align-items: center; justify-content: center; background: #cbd5e1; color: #334155; text-decoration: none;">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: #1e293b; font-size: 16px; font-weight: 600;">Jejak Audit Aktiviti</h3>
    
    <?php if (empty($logs)): ?>
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p>Tiada log audit ditemui.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">No.</th>
                        <th style="width: 180px;">Tarikh & Masa</th>
                        <th style="width: 220px;">Pentadbir (Emel)</th>
                        <th style="width: 200px;">Tindakan</th>
                        <th>Butiran Perincian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $bil = 1; foreach ($logs as $log): 
                        $tarikhStr = date('d/m/Y h:i A', strtotime($log['tarikh_cipta']));
                    ?>
                        <tr>
                            <td style="text-align: center; color: #64748b; font-size: 13px; vertical-align: middle; padding: 12px 8px;">
                                <?= $bil++; ?>
                            </td>
                            <td style="vertical-align: middle; font-size: 13px; color: #475569; padding: 12px 8px; white-space: nowrap;">
                                <?= $tarikhStr; ?>
                            </td>
                            <td style="vertical-align: middle; font-weight: 600; color: #334155; padding: 12px 8px; font-size: 13px;">
                                <?= htmlspecialchars($log['emel_pengguna']); ?>
                            </td>
                            <td style="vertical-align: middle; padding: 12px 8px;">
                                <span class="badge" style="background: #f1f5f9; color: #0f172a; border: 1px solid #e2e8f0; font-size: 12px; white-space: nowrap; font-weight: 600;">
                                    <?= htmlspecialchars($log['tindakan']); ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle; font-size: 13px; color: #475569; padding: 12px 8px; line-height: 1.4;">
                                <?= htmlspecialchars($log['butiran']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
