<?php
$permohonanCtrl = new PermohonanController();
$agreements = $permohonanCtrl->getActivePersetujuan();
?>
<h2>Hantar Permohonan</h2>

<div style="
    border:1px solid #ddd;
    padding:25px;
    border-radius:10px;
">

    <p>
        Sila pastikan semua maklumat adalah tepat sebelum menghantar permohonan.
    </p>

    <br>

    <form method="POST" action="?page=submit_permohonan">
        <?= csrfField(); ?>

        <div style="
            background:#fff8d6;
            border:1px solid #f0d96c;
            padding:20px;
            border-radius:8px;
            margin-bottom:20px;
        ">
            <strong style="font-size: 15px; display: block; margin-bottom: 15px; color: #854d0e;">Perakuan & Persetujuan:</strong>
            
            <?php if (!empty($agreements)): ?>
                <?php foreach ($agreements as $ag): ?>
                    <div style="margin-bottom: 15px; display: flex; align-items: flex-start; gap: 10px;">
                        <input type="checkbox" name="persetujuan[]" value="<?= $ag['id_persetujuan']; ?>" required style="margin-top: 3px; cursor: pointer;">
                        <span style="font-size: 14px; line-height: 1.5; color: #1e293b; cursor: pointer;">
                            <?= htmlspecialchars($ag['perihal']); ?> <span style="color: var(--danger);">*</span>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="margin-bottom: 15px; display: flex; align-items: flex-start; gap: 10px;">
                    <input type="checkbox" name="persetujuan[]" value="default" required style="margin-top: 3px; cursor: pointer;">
                    <span style="font-size: 14px; line-height: 1.5; color: #1e293b; cursor: pointer;">
                        Saya mengesahkan bahawa semua maklumat yang diberikan adalah benar dan tepat. <span style="color: var(--danger);">*</span>
                    </span>
                </div>
            <?php endif; ?>

        </div>

        <button type="submit" class="btn btn-teal" style="width:100%; padding: 14px 24px; font-size: 16px;">
            Hantar Permohonan
        </button>

    </form>

</div>