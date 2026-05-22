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

<div class="wizard-wrapper">
    <div class="wizard-card">
        <h1 class="wizard-heading">Borang Permohonan Pelajar</h1>
        <p class="wizard-subtitle">Lengkapkan setiap langkah untuk menghantar permohonan</p>

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

        <!-- FLASH MESSAGE -->
        <?php require_once "views/layouts/flash_message.php"; ?>

        <!-- PAGE CONTENT -->
        <?php require_once $content; ?>

        <div class="wizard-footer">
            <?php if ($current > 1): ?>
                <a href="?page=step<?= $current-1; ?>" class="btn btn-outline">← Kembali</a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>
            <?php if ($current < 6): ?>
                <button type="submit" form="stepForm" class="btn btn-teal">Seterusnya →</button>
            <?php endif; ?>
            <?php if ($current >= 1 && $current < 6): ?>
                <button type="submit" name="simpan_dan_keluar" value="1" form="stepForm" class="btn btn-outline" formnovalidate>Simpan & Keluar</button>
            <?php else: ?>
                <div></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "views/layouts/footer.php"; ?>