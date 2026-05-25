<?php
// Mapping data penjaga daripada array sequential $keluarga
$penjaga1 = $keluarga[0] ?? null;
$penjaga2 = $keluarga[1] ?? null;

// Penjaga 1 (Utama)
$jenis1 = $penjaga1['jenis_penjaga'] ?? 'Bapa Kandung';
if ($jenis1 === 'Bapa') {
    $jenis1 = 'Bapa Kandung';
}
if ($jenis1 === 'Ibu') {
    $jenis1 = 'Ibu Kandung';
}

$allowedOptions = ['Bapa Kandung', 'Bapa Tiri', 'Bapa Angkat', 'Ibu Kandung', 'Ibu Tiri', 'Ibu Angkat'];

if (in_array($jenis1, $allowedOptions)) {
    $select1 = $jenis1;
    $lain1 = '';
} else {
    $select1 = 'Lain';
    $lain1 = $jenis1;
}

// Penjaga 2 (Kedua)
$jenis2 = $penjaga2['jenis_penjaga'] ?? 'Tiada';
if ($jenis2 === 'Bapa') {
    $jenis2 = 'Bapa Kandung';
}
if ($jenis2 === 'Ibu') {
    $jenis2 = 'Ibu Kandung';
}

if (in_array($jenis2, array_merge(['Tiada'], $allowedOptions))) {
    $select2 = $jenis2;
    $lain2 = '';
} else {
    $select2 = 'Lain';
    $lain2 = $jenis2;
}
?>

<h2 style="margin-bottom:20px;">Maklumat Penjaga</h2>
<form method="POST" action="?page=save_step2" id="stepForm">
    <?= csrfField(); ?>

    <!-- PENJAGA UTAMA (WAJIB) -->
    <div style="border:1px solid var(--border); padding:25px; border-radius:12px; margin-bottom:25px; background: white; box-shadow: var(--shadow-sm);">
        <h3 style="color:var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(30, 86, 49, 0.1); font-size: 14px;">1</span>
            Penjaga Utama (Wajib)
        </h3>

        <div class="form-field">
            <label>Hubungan Penjaga Utama <span style="color: var(--danger);">*</span></label>
            <select name="hubungan_penjaga_1" id="hubungan_penjaga_1" required onchange="toggleCustomRel1()">
                <option value="Bapa Kandung" <?= $select1 === 'Bapa Kandung' ? 'selected' : ''; ?>>Bapa Kandung</option>
                <option value="Bapa Tiri" <?= $select1 === 'Bapa Tiri' ? 'selected' : ''; ?>>Bapa Tiri</option>
                <option value="Bapa Angkat" <?= $select1 === 'Bapa Angkat' ? 'selected' : ''; ?>>Bapa Angkat</option>
                <option value="Ibu Kandung" <?= $select1 === 'Ibu Kandung' ? 'selected' : ''; ?>>Ibu Kandung</option>
                <option value="Ibu Tiri" <?= $select1 === 'Ibu Tiri' ? 'selected' : ''; ?>>Ibu Tiri</option>
                <option value="Ibu Angkat" <?= $select1 === 'Ibu Angkat' ? 'selected' : ''; ?>>Ibu Angkat</option>
                <option value="Lain" <?= $select1 === 'Lain' ? 'selected' : ''; ?>>Lain-lain Hubungan (Sila nyatakan)</option>
            </select>
        </div>

        <div class="form-field" id="custom_rel_1_container" style="display: <?= $select1 === 'Lain' ? 'block' : 'none'; ?>;">
            <label>Nyatakan Hubungan Penjaga Utama <span style="color: var(--danger);">*</span></label>
            <input type="text" name="hubungan_penjaga_1_lain" id="hubungan_penjaga_1_lain" value="<?= htmlspecialchars($lain1); ?>" placeholder="Contoh: Datuk, Nenek, Bapa Saudara, Kakak">
        </div>

        <div class="form-field">
            <label>Nama Penuh <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_penjaga_1" value="<?= htmlspecialchars($penjaga1['nama_penuh'] ?? ''); ?>" required>
        </div>

        <div class="form-field">
            <label>No. Telefon <span style="color: var(--danger);">*</span></label>
            <div class="phone-wrapper">
                <span class="phone-prefix">+60</span>
                <input type="text" name="telefon_penjaga_1" 
                       value="<?= htmlspecialchars(str_replace('+60', '', $penjaga1['no_telefon'] ?? '')); ?>" 
                       required maxlength="12" data-phone-clean>
            </div>
        </div>

        <div class="form-field">
            <label>Pekerjaan <span style="color: var(--danger);">*</span></label>
            <input type="text" name="pekerjaan_penjaga_1" value="<?= htmlspecialchars($penjaga1['pekerjaan'] ?? ''); ?>" required>
        </div>

        <div class="form-field">
            <label>Pendapatan Bulanan (RM) <span style="color: var(--danger);">*</span></label>
            <input type="number" step="0.01" name="pendapatan_penjaga_1" value="<?= htmlspecialchars($penjaga1['pendapatan'] ?? ''); ?>" required>
        </div>

        <div class="form-field">
            <label>Alamat Kediaman <span style="color: var(--danger);">*</span></label>
            <textarea name="alamat_penjaga_1" required><?= htmlspecialchars($penjaga1['alamat'] ?? ''); ?></textarea>
        </div>

        <div class="form-field">
            <label>Alamat Emel</label>
            <input type="email" name="emel_penjaga_1" value="<?= htmlspecialchars($penjaga1['emel'] ?? ''); ?>">
        </div>
    </div>

    <!-- PENJAGA KEDUA (OPSIONAL) -->
    <div style="border:1px solid var(--border); padding:25px; border-radius:12px; margin-bottom:25px; background: white; box-shadow: var(--shadow-sm);">
        <h3 style="color:var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(30, 86, 49, 0.1); font-size: 14px;">2</span>
            Penjaga Kedua (Pilihan)
        </h3>

        <div class="form-field">
            <label>Hubungan Penjaga Kedua</label>
            <select name="hubungan_penjaga_2" id="hubungan_penjaga_2" onchange="togglePenjagaKedua()">
                <option value="Tiada" <?= $select2 === 'Tiada' ? 'selected' : ''; ?>>Tiada (Hanya Penjaga Utama)</option>
                <option value="Bapa Kandung" <?= $select2 === 'Bapa Kandung' ? 'selected' : ''; ?>>Bapa Kandung</option>
                <option value="Bapa Tiri" <?= $select2 === 'Bapa Tiri' ? 'selected' : ''; ?>>Bapa Tiri</option>
                <option value="Bapa Angkat" <?= $select2 === 'Bapa Angkat' ? 'selected' : ''; ?>>Bapa Angkat</option>
                <option value="Ibu Kandung" <?= $select2 === 'Ibu Kandung' ? 'selected' : ''; ?>>Ibu Kandung</option>
                <option value="Ibu Tiri" <?= $select2 === 'Ibu Tiri' ? 'selected' : ''; ?>>Ibu Tiri</option>
                <option value="Ibu Angkat" <?= $select2 === 'Ibu Angkat' ? 'selected' : ''; ?>>Ibu Angkat</option>
                <option value="Lain" <?= $select2 === 'Lain' ? 'selected' : ''; ?>>Lain-lain Hubungan (Sila nyatakan)</option>
            </select>
        </div>

        <div class="form-field" id="custom_rel_2_container" style="display: <?= $select2 === 'Lain' ? 'block' : 'none'; ?>;">
            <label>Nyatakan Hubungan Penjaga Kedua <span style="color: var(--danger);">*</span></label>
            <input type="text" name="hubungan_penjaga_2_lain" id="hubungan_penjaga_2_lain" value="<?= htmlspecialchars($lain2); ?>" placeholder="Contoh: Datuk, Nenek, Ibu Saudara, Abang">
        </div>

        <div id="penjaga_2_fields" style="display: <?= $select2 === 'Tiada' ? 'none' : 'block'; ?>;">
            <div class="form-field">
                <label>Nama Penuh <span style="color: var(--danger);">*</span></label>
                <input type="text" name="nama_penjaga_2" value="<?= htmlspecialchars($penjaga2['nama_penuh'] ?? ''); ?>" data-compulsory>
            </div>

            <div class="form-field">
                <label>No. Telefon <span style="color: var(--danger);">*</span></label>
                <div class="phone-wrapper">
                    <span class="phone-prefix">+60</span>
                    <input type="text" name="telefon_penjaga_2" 
                           value="<?= htmlspecialchars(str_replace('+60', '', $penjaga2['no_telefon'] ?? '')); ?>" 
                           data-compulsory maxlength="12" data-phone-clean>
                </div>
            </div>

            <div class="form-field">
                <label>Pekerjaan <span style="color: var(--danger);">*</span></label>
                <input type="text" name="pekerjaan_penjaga_2" value="<?= htmlspecialchars($penjaga2['pekerjaan'] ?? ''); ?>" data-compulsory>
            </div>

            <div class="form-field">
                <label>Pendapatan Bulanan (RM) <span style="color: var(--danger);">*</span></label>
                <input type="number" step="0.01" name="pendapatan_penjaga_2" value="<?= htmlspecialchars($penjaga2['pendapatan'] ?? ''); ?>" data-compulsory>
            </div>

            <div class="form-field">
                <label>Alamat Kediaman <span style="color: var(--danger);">*</span></label>
                <textarea name="alamat_penjaga_2" data-compulsory><?= htmlspecialchars($penjaga2['alamat'] ?? ''); ?></textarea>
            </div>

            <div class="form-field">
                <label>Alamat Emel</label>
                <input type="email" name="emel_penjaga_2" value="<?= htmlspecialchars($penjaga2['emel'] ?? ''); ?>">
            </div>
        </div>
    </div>

    <div style="display:none;"><button type="submit">Simpan</button></div>
</form>

<script>
function toggleCustomRel1() {
    const select = document.getElementById('hubungan_penjaga_1');
    const container = document.getElementById('custom_rel_1_container');
    const input = document.getElementById('hubungan_penjaga_1_lain');
    if (select.value === 'Lain') {
        container.style.display = 'block';
        input.required = true;
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

function togglePenjagaKedua() {
    const select = document.getElementById('hubungan_penjaga_2');
    const fieldsContainer = document.getElementById('penjaga_2_fields');
    const customRelContainer = document.getElementById('custom_rel_2_container');
    const customRelInput = document.getElementById('hubungan_penjaga_2_lain');
    
    // Get all required inputs in the secondary card
    const requiredInputs = fieldsContainer.querySelectorAll('[data-compulsory]');

    if (select.value === 'Tiada') {
        fieldsContainer.style.display = 'none';
        customRelContainer.style.display = 'none';
        
        requiredInputs.forEach(input => {
            input.required = false;
        });
        customRelInput.required = false;
    } else {
        fieldsContainer.style.display = 'block';
        
        requiredInputs.forEach(input => {
            input.required = true;
        });
        
        if (select.value === 'Lain') {
            customRelContainer.style.display = 'block';
            customRelInput.required = true;
        } else {
            customRelContainer.style.display = 'none';
            customRelInput.required = false;
            customRelInput.value = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleCustomRel1();
    togglePenjagaKedua();
});
</script>
