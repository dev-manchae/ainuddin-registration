<?php require_once "views/layouts/header.php"; ?>

<style>
    .wizard-wrapper {
        max-width: 800px;
        margin: 50px auto;
    }
    .wizard-card {
        background: white;
        border-radius: 20px;
        padding: 40px 35px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
    }
    .wizard-heading {
        text-align: center;
        margin-bottom: 5px;
        color: #1e5631;
        font-weight: 700;
        font-size: 26px;
    }
    .wizard-subtitle {
        text-align: center;
        color: #64748b;
        font-size: 15px;
        margin-bottom: 30px;
    }
    .step-indicator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        position: relative;
    }
    .step-dot {
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 60px;
    }
    .step-dot.clickable {
        text-decoration: none;
        cursor: pointer;
    }
    .step-dot.clickable:hover .step-circle {
        box-shadow: 0 0 0 3px rgba(0,137,123,0.2);
    }
    .step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 15px;
        background: #e2e8f0;
        color: #94a3b8;
        transition: background 0.2s, color 0.2s;
    }
    .step-dot.active .step-circle,
    .step-dot.completed .step-circle {
        background: #00897b;
        color: white;
    }
    .step-label {
        font-size: 11px;
        margin-top: 5px;
        color: #94a3b8;
        font-weight: 500;
        text-align: center;
    }
    .step-dot.active .step-label,
    .step-dot.completed .step-label {
        color: #00897b;
        font-weight: 600;
    }
    .step-dot.locked .step-label {
        color: #cbd5e1;
    }
    .progress-line {
        position: absolute;
        top: 19px;
        left: 40px;
        right: 40px;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .progress-line-fill {
        height: 100%;
        background: #00897b;
        transition: width 0.3s;
    }
    .wizard-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        align-items: center;
    }
</style>

<?php
$isSuccessPage = (isset($content) && $content === "views/registration/success.php");
?>

<div class="wizard-wrapper">
    <?php if (!$isSuccessPage): ?>
        <div class="student-header">
            <div>
                <h2>Borang Permohonan Pelajar</h2>
                <div class="subtext">Lengkapkan setiap langkah untuk menghantar permohonan</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="wizard-card" style="position: relative;">
        <!-- Auto-save Status Indicator -->
        <div id="autosave-status" class="autosave-indicator" style="display: none;"></div>
        <?php if (!$isSuccessPage): ?>
            <?php
            $steps = [
                1 => 'Pelajar',
                2 => 'Penjaga',
                3 => 'Akademik',
                4 => 'Kesihatan',
                5 => 'Dokumen',
                6 => 'Hantar'
            ];
            
            $current = 1;
            if (isset($page) && preg_match('/^step(\d+)$/', $page, $matches)) {
                $current = (int)$matches[1];
            }
            $max_unlocked = $langkah_semasa ?? 1;
            ?>
            <div class="step-indicator">
                <?php foreach ($steps as $num => $label): ?>
                    <?php
                    $isCompleted = $max_unlocked > $num;
                    $isActive = $current == $num;
                    $isReachable = $max_unlocked >= $num;
                    $classes = [];
                    if ($isActive) $classes[] = 'active';
                    if ($isCompleted) $classes[] = 'completed';
                    if (!$isReachable) $classes[] = 'locked';
                    $classStr = implode(' ', $classes);
                    ?>
                    <?php if ($isReachable): ?>
                        <a href="?page=step<?= $num; ?>" class="step-dot clickable <?= $classStr; ?>">
                            <div class="step-circle"><?= $isCompleted ? '✓' : $num; ?></div>
                            <span class="step-label"><?= $label; ?></span>
                        </a>
                    <?php else: ?>
                        <span class="step-dot <?= $classStr; ?>">
                            <div class="step-circle"><?= $num; ?></div>
                            <span class="step-label"><?= $label; ?></span>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="progress-line">
                    <div class="progress-line-fill" style="width: <?= (($current-1)/5)*100 ?>%;"></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- FLASH MESSAGE -->
        <?php require_once "views/layouts/flash_message.php"; ?>

        <!-- PAGE CONTENT -->
        <?php require_once $content; ?>

        <?php if (!$isSuccessPage): ?>
            <div class="wizard-footer">
                <div>
                    <?php if ($current > 1): ?>
                        <a href="?page=step<?= $current-1; ?>" class="btn btn-outline">← Kembali</a>
                    <?php endif; ?>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <?php if ($current >= 1 && $current < 6): ?>
                        <button type="submit" name="simpan_dan_keluar" value="1" form="stepForm" class="btn btn-outline" formnovalidate>Simpan & Keluar</button>
                    <?php endif; ?>
                    <?php if ($current < 6): ?>
                        <button type="submit" form="stepForm" class="btn btn-teal">Seterusnya →</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Session Timeout Modal -->
<div id="session-timeout-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); max-width: 450px; width: 100%; text-align: center; border: 1px solid #e2e8f0; font-family: inherit;">
        <div style="font-size: 40px; margin-bottom: 15px;">⏳</div>
        <h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 700; color: #1e293b;">Sesi Anda Hampir Tamat</h3>
        <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.5; color: #64748b;">
            Oleh kerana tiada aktiviti dikesan, sesi anda akan tamat secara automatik dalam masa <strong id="session-countdown" style="color: #ef4444; font-size: 16px;">3:00</strong>. Sila klik butang di bawah untuk kekal log masuk.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="session-keep-btn" class="btn btn-teal" style="padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; height: 42px;">Kekalkan Sesi</button>
            <button id="session-logout-btn" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; background: #f1f5f9; color: #475569; height: 42px;">Log Keluar</button>
        </div>
    </div>
</div>

<?php require_once "views/layouts/footer.php"; ?>