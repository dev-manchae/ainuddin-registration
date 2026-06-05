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

// Query active intake batch
$activeIntake = $permCtrl->getActiveIntake();
?>

<style>
    /* Greeting header layout */
    .student-header {
        background: linear-gradient(135deg, #1e5631, #133c22);
        border-radius: 16px;
        padding: 30px;
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px -5px rgba(30, 86, 49, 0.15), 0 8px 10px -6px rgba(30, 86, 49, 0.15);
        border: 1px solid rgba(255,255,255,0.05);
        position: relative;
        overflow: hidden;
    }
    .student-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    .student-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #ffffff !important;
        margin-bottom: 6px;
    }
    .student-header .subtext {
        color: rgba(255, 255, 255, 0.8) !important;
        font-size: 14px;
    }
    .student-header .session-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(212, 175, 55, 0.15);
        border: 1px solid rgba(212, 175, 55, 0.3);
        color: #fce79f;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 10px;
        width: fit-content;
    }

    .btn-permohonan {
        background: #d4af37; /* Gold accent */
        color: #0f2e1a !important;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        height: fit-content;
        font-family: inherit;
    }
    .btn-permohonan:hover {
        background: #f1c40f;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(212, 175, 55, 0.4);
    }
    .btn-permohonan.disabled {
        background: rgba(255,255,255,0.15) !important;
        color: rgba(255,255,255,0.4) !important;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* Stats Grid */
    .stat-row { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); 
        gap: 15px; 
        margin-bottom: 30px; 
    }
    .stat-item { 
        background: white; 
        border-radius: 16px; 
        padding: 20px; 
        text-align: center; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.02); 
        border: 1px solid #e2e8f0; 
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(30, 86, 49, 0.05);
        border-color: #cbd5e1;
    }
    .stat-item .stat-number { 
        font-size: 32px; 
        font-weight: 800; 
        color: #1e5631; 
        margin-bottom: 2px; 
    }
    .stat-item .stat-label { 
        font-size: 13px; 
        color: #64748b; 
        font-weight: 600; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Table styling */
    .app-table { 
        width: 100%; 
        border-collapse: collapse; 
        background: white; 
        border-radius: 12px; 
        overflow: hidden; 
    }
    .app-table th { 
        background: #f8fafc; 
        font-weight: 700; 
        color: #475569; 
        padding: 16px 20px; 
        font-size: 13px; 
        text-align: left; 
        border-bottom: 1px solid #e2e8f0; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .app-table td { 
        padding: 16px 20px; 
        border-bottom: 1px solid #f1f5f9; 
        font-size: 14px; 
        color: #334155; 
    }
    .app-table tr:last-child td { border-bottom: none; }
    .app-table tr:hover td { background: #f8fafc; }
    
    .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-draft { background: #fef3c7; color: #92400e; }
    .badge-submitted { background: #dbeafe; color: #1e40af; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-warning { background: #fef3c7; color: #d97706; }

    /* Action buttons in table */
    .action-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    
    .action-link { 
        background: #f1f5f9; 
        color: #475569; 
        padding: 8px 16px; 
        border-radius: 8px; 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 13px; 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        border: 1px solid #e2e8f0; 
        cursor: pointer; 
        transition: all 0.2s ease; 
        font-family: inherit; 
    }
    .action-link:hover { 
        background: #e2e8f0; 
        color: #1e293b;
        border-color: #cbd5e1;
    }
    .action-resume {
        background: #dcfce7;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .action-resume:hover {
        background: #bbf7d0;
        color: #166534;
        border-color: #86efac;
    }
    .action-update {
        background: #fef3c7;
        color: #b45309;
        border-color: #fde68a;
    }
    .action-update:hover {
        background: #fde68a;
        color: #78350f;
        border-color: #fcd34d;
    }
    .action-delete { 
        background: #fee2e2; 
        color: #b91c1c; 
        border-color: #fecaca;
    }
    .action-delete:hover { 
        background: #fecaca; 
        color: #991b1b;
        border-color: #fca5a5;
    }
    .action-pdf {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .action-pdf:hover {
        background: #e2e8f0;
        border-color: #94a3b8;
    }
    .action-letter {
        background: #e0f2f1;
        color: #00796b;
        border-color: #b2dfdb;
    }
    .action-letter:hover {
        background: #b2dfdb;
        border-color: #80cbc4;
    }

    .empty-message { background: white; border-radius: 12px; padding: 60px 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; color: #64748b; }

    /* Stepper styles */
    .timeline-stepper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        margin-top: 15px;
        padding: 10px 0;
    }
    .timeline-line {
        position: absolute;
        top: 29px;
        left: 12.5%;
        right: 12.5%;
        height: 6px;
        background: #f1f5f9;
        z-index: 1;
        border-radius: 3px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }
    .timeline-line-fill {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        z-index: 2;
        transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        border-radius: 3px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 25%;
        flex: 0 0 25%;
        text-align: center;
        z-index: 3;
        position: relative;
        cursor: default;
    }
    .step-item-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .step-item:hover .step-item-circle {
        transform: scale(1.1);
    }
    .step-label-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 12px;
    }
    .step-item-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .step-item-desc {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        font-weight: 500;
    }
    
    /* Table scroll wrapper */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .table-responsive .app-table {
        margin: 0;
        border: none;
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .student-header {
            flex-direction: column;
            align-items: stretch;
            gap: 20px;
            text-align: center;
            padding: 24px;
        }
        .student-header .session-badge {
            margin: 10px auto 0 auto;
        }
        .btn-permohonan {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 600px) {
        .timeline-stepper {
            flex-direction: column;
            align-items: stretch;
            gap: 24px;
            padding-left: 10px;
        }
        .timeline-line {
            display: none;
        }
        .step-item {
            flex-direction: row;
            width: 100% !important;
            text-align: left;
            align-items: center;
            gap: 16px;
        }
        .step-item::after {
            content: '';
            position: absolute;
            left: 22px;
            top: 44px;
            bottom: -32px;
            width: 3px;
            background: #cbd5e1;
            z-index: 1;
        }
        .step-item:last-child::after {
            display: none;
        }
        .step-item-1::after { background: var(--step-2-border); }
        .step-item-2::after { background: var(--step-3-border); }
        .step-item-3::after { background: var(--step-4-border); }
        
        .step-label-wrapper {
            align-items: flex-start;
            margin-top: 0;
            text-align: left;
        }
    }

    @media (max-width: 480px) {
        .stat-row {
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 10px;
        }
    }
</style>

<div class="student-header">
    <div>
        <h2>Selamat datang, <?= htmlspecialchars($_SESSION['nama_penuh']); ?></h2>
        <div class="subtext">
            Urus permohonan pendaftaran pelajar anda
            <?php if ($activeIntake): ?>
                <div class="session-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Sesi Aktif: <?= htmlspecialchars($activeIntake['nama_intake']); ?> (Tutup: <?= date('d/m/Y', strtotime($activeIntake['tarikh_tutup'])); ?>)
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($hasActive): ?>
        <button class="btn-permohonan disabled" disabled title="Anda mempunyai permohonan aktif. Sila lengkapkan atau padam sebelum membuat yang baru.">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Permohonan Baru
        </button>
    <?php elseif (!$activeIntake): ?>
        <button class="btn-permohonan disabled" disabled title="Pendaftaran ditutup buat sementara waktu.">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Permohonan Baru
        </button>
    <?php else: ?>
        <a href="?page=mula_permohonan" class="btn-permohonan">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Permohonan Baru
        </a>
    <?php endif; ?>
</div>

<?php if (!$activeIntake): ?>
    <div class="alert alert-info" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); font-family: inherit;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; color: #334155;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            Pendaftaran Ditutup
        </h4>
        <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #475569;">
            Maaf, sesi pendaftaran pelajar baharu Tahfiz Ainuddin telah ditutup buat sementara waktu atau kuota pengambilan pelajar bagi sesi semasa telah penuh. Sila hubungi pentadbiran untuk maklumat lanjut.
        </p>
    </div>
<?php endif; ?>

<?php if ($revisionAlert): ?>
    <div class="alert alert-error" style="background: #fffbeb; border: 1px solid #fde68a; color: #b45309; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); font-family: inherit;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; color: #b45309;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            Tindakan Diperlukan: Kemaskini Maklumat Permohonan
        </h4>
        <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 1.5; color: #78350f;">
            Pentadbir (<strong><?= htmlspecialchars($revisionAlert['nama_admin'] ?? 'Sistem'); ?></strong>) telah meminta pembetulan pada permohonan anda dengan catatan:
        </p>
        
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <div style="background: white; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-style: italic; color: #78350f; font-weight: 500; font-size: 14px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); min-height: 46px; display: flex; align-items: center;">
                    "<?= nl2br(htmlspecialchars($revisionAlert['catatan'])); ?>"
                </div>
            </div>
            <div style="flex-shrink: 0;">
                <a href="?page=resume_permohonan&id=<?= $revisionAlert['id_permohonan']; ?>" class="btn-permohonan" style="background: #d97706; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: white; border-radius: 8px; font-weight: 600; padding: 10px 22px; height: 46px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Mula Kemaskini
                </a>
            </div>
        </div>
        
        <small style="display: block; margin-top: 8px; color: #b45309; opacity: 0.8; font-size: 11px;">
            Diminta pada: <?= date('d/m/Y H:i', strtotime($revisionAlert['tarikh'])); ?>
        </small>
    </div>
<?php endif; ?>

<div class="stat-row">
    <div class="stat-item"><div class="stat-number"><?= $total; ?></div><div class="stat-label">Jumlah</div></div>
    <div class="stat-item"><div class="stat-number"><?= $draft; ?></div><div class="stat-label">Draf</div></div>
    <div class="stat-item"><div class="stat-number"><?= $submitted; ?></div><div class="stat-label">Dihantar</div></div>
    <div class="stat-item"><div class="stat-number"><?= $approved; ?></div><div class="stat-label">Diluluskan</div></div>
    <div class="stat-item"><div class="stat-number"><?= $rejected; ?></div><div class="stat-label">Ditolak</div></div>
</div>

<?php if (!empty($applications)): ?>
    <?php
    $activeApp = $applications[0]; // Most recent application
    $status = $activeApp['kod_status'];
    
    // Calculate progress state
    $progressPercent = 0;
    $progressColor = '#1e5631'; // Default Forest Green
    
    // Step 1: Draf (Default pending state)
    $step1Bg = '#ffffff'; $step1Border = '#cbd5e1'; $step1Text = '#94a3b8';
    // Step 2: Dihantar
    $step2Bg = '#ffffff'; $step2Border = '#cbd5e1'; $step2Text = '#94a3b8';
    // Step 3: Semakan
    $step3Bg = '#ffffff'; $step3Border = '#cbd5e1'; $step3Text = '#94a3b8'; $step3Subtext = 'Syarat & Temuduga';
    // Step 4: Keputusan
    $step4Bg = '#ffffff'; $step4Border = '#cbd5e1'; $step4Text = '#94a3b8'; $step4Subtext = 'Lulus / Ditolak';
    
    if ($status === '00') {
        $progressPercent = 0;
        $step1Bg = '#dcfce7'; $step1Border = '#16a34a'; $step1Text = '#15803d';
    } elseif ($status === '03') {
        $progressPercent = 66.6;
        $step1Bg = '#16a34a'; $step1Border = '#16a34a'; $step1Text = '#ffffff';
        $step2Bg = '#16a34a'; $step2Border = '#16a34a'; $step2Text = '#ffffff';
        $step3Bg = '#eaf5ee'; $step3Border = '#16a34a'; $step3Text = '#15803d'; $step3Subtext = 'Sedang Disemak';
    } elseif ($status === '08') {
        $progressPercent = 66.6;
        $progressColor = '#d97706'; // Amber/Gold warning
        $step1Bg = '#d97706'; $step1Border = '#d97706'; $step1Text = '#ffffff';
        $step2Bg = '#d97706'; $step2Border = '#d97706'; $step2Text = '#ffffff';
        $step3Bg = '#fef3c7'; $step3Border = '#d97706'; $step3Text = '#b45309'; $step3Subtext = 'Tindakan Diperlukan';
    } elseif ($status === '04') {
        $progressPercent = 100;
        $progressColor = '#16a34a'; // Success green
        $step1Bg = '#16a34a'; $step1Border = '#16a34a'; $step1Text = '#ffffff';
        $step2Bg = '#16a34a'; $step2Border = '#16a34a'; $step2Text = '#ffffff';
        $step3Bg = '#16a34a'; $step3Border = '#16a34a'; $step3Text = '#ffffff'; $step3Subtext = 'Selesai Disemak';
        $step4Bg = '#dcfce7'; $step4Border = '#16a34a'; $step4Text = '#15803d'; $step4Subtext = 'Tahniah! Lulus';
    } elseif ($status === '05') {
        $progressPercent = 100;
        $progressColor = '#ef4444'; // Error red
        $step1Bg = '#ef4444'; $step1Border = '#ef4444'; $step1Text = '#ffffff';
        $step2Bg = '#ef4444'; $step2Border = '#ef4444'; $step2Text = '#ffffff';
        $step3Bg = '#ef4444'; $step3Border = '#ef4444'; $step3Text = '#ffffff'; $step3Subtext = 'Selesai Disemak';
        $step4Bg = '#fee2e2'; $step4Border = '#ef4444'; $step4Text = '#991b1b'; $step4Subtext = 'Ditolak';
    }
    ?>
    
    <!-- Premium Application Status Timeline Stepper -->
    <div class="card timeline-card" style="margin-bottom: 30px; padding: 24px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.006);">
        <h4 style="margin: 0 0 24px 0; font-size: 15px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e5631" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            Status Alur Permohonan Pelajar: <span style="color: #1e5631; font-weight: 800;"><?= htmlspecialchars($activeApp['nama_pelajar'] ?: 'Tanpa Nama'); ?></span>
        </h4>
        
        <div class="timeline-stepper" style="--step-2-border: <?= $step2Border; ?>; --step-3-border: <?= $step3Border; ?>; --step-4-border: <?= $step4Border; ?>;">
            <!-- Timeline connector line -->
            <div class="timeline-line">
                <!-- Progress fill line depending on status -->
                <div class="timeline-line-fill" style="width: <?= $progressPercent; ?>%; background: <?= $progressColor; ?>;"></div>
            </div>
            
            <!-- Step 1: Draf -->
            <div class="step-item step-item-1">
                <div class="step-item-circle" style="background: <?= $step1Bg; ?>; border: 3px solid <?= $step1Border; ?>; color: <?= $step1Text; ?>;">
                    1
                </div>
                <div class="step-label-wrapper">
                    <span class="step-item-title">Draf</span>
                    <span class="step-item-desc">Mengisi Maklumat</span>
                </div>
            </div>
            
            <!-- Step 2: Dihantar -->
            <div class="step-item step-item-2">
                <div class="step-item-circle" style="background: <?= $step2Bg; ?>; border: 3px solid <?= $step2Border; ?>; color: <?= $step2Text; ?>;">
                    2
                </div>
                <div class="step-label-wrapper">
                    <span class="step-item-title">Dihantar</span>
                    <span class="step-item-desc">Diterima Sistem</span>
                </div>
            </div>
            
            <!-- Step 3: Semakan -->
            <div class="step-item step-item-3">
                <div class="step-item-circle" style="background: <?= $step3Bg; ?>; border: 3px solid <?= $step3Border; ?>; color: <?= $step3Text; ?>;">
                    3
                </div>
                <div class="step-label-wrapper">
                    <span class="step-item-title">Semakan Dokumen</span>
                    <span class="step-item-desc"><?= $step3Subtext; ?></span>
                </div>
            </div>
            
            <!-- Step 4: Keputusan -->
            <div class="step-item step-item-4">
                <div class="step-item-circle" style="background: <?= $step4Bg; ?>; border: 3px solid <?= $step4Border; ?>; color: <?= $step4Text; ?>;">
                    4
                </div>
                <div class="step-label-wrapper">
                    <span class="step-item-title">Keputusan</span>
                    <span class="step-item-desc"><?= $step4Subtext; ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<h3 style="margin: 0 0 15px; font-size: 18px; color: #1e293b;">Senarai Permohonan</h3>

<?php if (!empty($applications)): ?>
    <div class="table-responsive">
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
                                    <?php 
                                    $currentDate = date('Y-m-d');
                                    $intakeActive = ($app['intake_status'] ?? 'N') === 'Y';
                                    $isClosed = ($currentDate < ($app['intake_buka'] ?? '1970-01-01') || $currentDate > ($app['intake_tutup'] ?? '9999-12-31'));
                                    ?>
                                    <?php if (!$intakeActive || $isClosed): ?>
                                        <span class="badge badge-rejected" style="background: #cbd5e1; color: #475569; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;" title="Sesi pendaftaran telah tamat tempoh atau ditutup.">Tamat Tempoh</span>
                                    <?php else: ?>
                                        <a href="?page=resume_permohonan&id=<?= $app['id_permohonan']; ?>" class="action-link action-resume">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                            Sambung
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="?page=delete_permohonan" onsubmit="return confirm('Adakah anda pasti ingin memadam draf ini?');" style="display:inline-block; margin:0;">
                                        <?= csrfField(); ?>
                                        <input type="hidden" name="id_permohonan" value="<?= $app['id_permohonan']; ?>">
                                        <button type="submit" class="action-link action-delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            Padam
                                        </button>
                                    </form>
                                <?php elseif ($app['kod_status'] == '08'): ?>
                                    <?php 
                                    $intakeActive = ($app['intake_status'] ?? 'N') === 'Y';
                                    ?>
                                    <?php if (!$intakeActive): ?>
                                        <span class="badge badge-rejected" style="background: #cbd5e1; color: #475569; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;" title="Sesi pendaftaran telah ditutup sepenuhnya.">Sesi Ditutup</span>
                                    <?php else: ?>
                                        <a href="?page=resume_permohonan&id=<?= $app['id_permohonan']; ?>" class="action-link action-update">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Kemaskini
                                        </a>
                                    <?php endif; ?>
                                    <a href="?page=cetak_profil&id=<?= $app['id_permohonan']; ?>" target="_blank" class="action-link action-pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                        Profil PDF
                                    </a>
                                <?php elseif ($app['kod_status'] == '04'): ?>
                                    <a href="?page=cetak_surat_tawaran" target="_blank" class="action-link action-letter">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                        Surat Tawaran
                                    </a>
                                    <a href="?page=download_peraturan" target="_blank" class="action-link action-pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                        Surat Peraturan
                                    </a>
                                    <a href="?page=cetak_profil&id=<?= $app['id_permohonan']; ?>" target="_blank" class="action-link action-pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                        Profil PDF
                                    </a>
                                <?php else: ?>
                                    <a href="?page=cetak_profil&id=<?= $app['id_permohonan']; ?>" target="_blank" class="action-link action-pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                        Profil PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-message">
        <p>Belum ada permohonan. Klik "+ Permohonan Baru" untuk mula.</p>
    </div>
<?php endif; ?>