<h2 style="margin-bottom: 5px;">Senarai Permohonan</h2>
<p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Cari dan urus permohonan masuk</p>

<!-- FILTERS -->
<div class="card">
    <form method="GET">
        <input type="hidden" name="page" value="admin_senarai">

        <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 2; min-width: 200px; margin-bottom: 0;">
                <label>Carian</label>
                <input 
                    type="text" 
                    name="carian" 
                    placeholder="Nama, No KP, No Rujukan..."
                    value="<?= htmlspecialchars($_GET['carian'] ?? ''); ?>"
                >
            </div>

            <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                <label>Status</label>
                <select name="kod_status">
                    <option value="">-- Semua --</option>
                    <?php foreach ($statusList as $status): ?>
                        <option 
                            value="<?= $status['kod']; ?>"
                            <?= (($_GET['kod_status'] ?? '') == $status['kod']) ? 'selected' : ''; ?>
                        >
                            <?= $status['perihal']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom: 0;">
                <label>Sesi (Tahun)</label>
                <select name="tahun">
                    <option value="">-- Semua --</option>
                    <?php foreach ($yearsList as $yr): ?>
                        <option 
                            value="<?= $yr; ?>"
                            <?= (($_GET['tahun'] ?? '') == $yr) ? 'selected' : ''; ?>
                        >
                            <?= $yr; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                <label>Tarikh Dari</label>
                <input 
                    type="date" 
                    name="tarikh_dari" 
                    value="<?= htmlspecialchars($_GET['tarikh_dari'] ?? ''); ?>"
                >
            </div>

            <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                <label>Tarikh Hingga</label>
                <input 
                    type="date" 
                    name="tarikh_hingga" 
                    value="<?= htmlspecialchars($_GET['tarikh_hingga'] ?? ''); ?>"
                >
            </div>

            <div class="form-group" style="display: flex; gap: 8px; align-items: end; margin-bottom: 0;">
                <div style="display: flex; flex-direction: column; width: 100%;">
                    <label style="visibility: hidden; height: 20px;">Tapis</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-primary">Tapis</button>
                        <?php if (!empty($_GET['carian']) || !empty($_GET['kod_status']) || !empty($_GET['tahun']) || !empty($_GET['tarikh_dari']) || !empty($_GET['tarikh_hingga'])): ?>
                            <a href="?page=admin_senarai" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                        <?php
                        $exportUrl = "?page=admin_export_csv";
                        if (!empty($_GET['carian'])) {
                            $exportUrl .= "&carian=" . urlencode($_GET['carian']);
                        }
                        if (!empty($_GET['kod_status'])) {
                            $exportUrl .= "&kod_status=" . urlencode($_GET['kod_status']);
                        }
                        if (!empty($_GET['tahun'])) {
                            $exportUrl .= "&tahun=" . urlencode($_GET['tahun']);
                        }
                        if (!empty($_GET['tarikh_dari'])) {
                            $exportUrl .= "&tarikh_dari=" . urlencode($_GET['tarikh_dari']);
                        }
                        if (!empty($_GET['tarikh_hingga'])) {
                            $exportUrl .= "&tarikh_hingga=" . urlencode($_GET['tarikh_hingga']);
                        }
                        ?>
                        <a href="<?= $exportUrl; ?>" class="btn" style="background: #047857; color: white; display: inline-flex; align-items: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Eksport CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- TABLE -->
<div class="card" style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; min-width: 950px;">
        <thead>
            <tr>
                <th style="width: 130px;">No Rujukan</th>
                <th>Pemohon</th>
                <th>Pelajar</th>
                <th style="width: 140px;">No KP</th>
                <th>Program</th>
                <th style="width: 140px; text-align: center;">Status</th>
                <th style="width: 120px;">Tarikh Hantar</th>
                <th style="width: 100px;">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($applications)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748b;">Tiada permohonan ditemui.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($applications as $row): ?>
                    <tr>
                        <td style="white-space: nowrap; font-weight: 600;"><?= htmlspecialchars($row['no_rujukan'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['nama_pemohon']); ?></td>
                        <td><?= htmlspecialchars($row['nama_pelajar'] ?? '-'); ?></td>
                        <td style="white-space: nowrap;"><?= htmlspecialchars($row['no_kp'] ?? '-'); ?></td>
                        <td style="white-space: nowrap;"><?= htmlspecialchars($row['program'] ?? '-'); ?></td>
                        <td style="text-align: center; vertical-align: middle;">
                            <?php
                            $badgeClass = 'badge-draft';
                            if ($row['kod_status'] == '03') $badgeClass = 'badge-submitted';
                            elseif ($row['kod_status'] == '04') $badgeClass = 'badge-approved';
                            elseif ($row['kod_status'] == '05') $badgeClass = 'badge-rejected';
                            elseif ($row['kod_status'] == '08') $badgeClass = 'badge-warning';

                            $statusLabel = match($row['kod_status']) {
                                '00' => 'Draf',
                                '03' => 'Dihantar',
                                '04' => 'Diluluskan',
                                '05' => 'Ditolak',
                                '08' => 'Perlu Kemaskini',
                                default => $row['kod_status']
                             };
                            ?>
                            <span class="badge <?= $badgeClass; ?>"><?= $statusLabel; ?></span>
                        </td>
                        <td style="white-space: nowrap;">
                            <?= $row['tarikh_hantar'] ? date('d/m/Y', strtotime($row['tarikh_hantar'])) : '-'; ?>
                        </td>
                        <td style="white-space: nowrap; vertical-align: middle;">
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <a href="?page=admin_lihat&id=<?= $row['id_permohonan']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;">Lihat</a>
                                <?php if ($row['kod_status'] == '04'): ?>
                                    <a href="?page=admin_cetak_surat_tawaran&id=<?= $row['id_permohonan']; ?>" target="_blank" class="btn btn-teal" style="background: var(--teal); color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        Surat
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchQuery = <?= json_encode($_GET['carian'] ?? ''); ?>;
    const query = searchQuery.trim().toLowerCase();
    if (!query) return;

    const table = document.querySelector("table tbody");
    if (!table) return;

    const walker = document.createTreeWalker(table, NodeFilter.SHOW_TEXT, null, false);
    const textNodes = [];
    while (walker.nextNode()) {
        textNodes.push(walker.currentNode);
    }

    textNodes.forEach(node => {
        const text = node.nodeValue;
        const lowerText = text.toLowerCase();
        if (lowerText.includes(query)) {
            const span = document.createElement("span");
            let index = 0;
            while (index < text.length) {
                const pos = lowerText.indexOf(query, index);
                if (pos === -1) {
                    span.appendChild(document.createTextNode(text.substring(index)));
                    break;
                }
                if (pos > index) {
                    span.appendChild(document.createTextNode(text.substring(index, pos)));
                }
                const mark = document.createElement("mark");
                mark.textContent = text.substring(pos, pos + query.length);
                mark.style.background = "#fef08a";
                mark.style.color = "#854d0e";
                mark.style.padding = "1px 3px";
                mark.style.borderRadius = "4px";
                span.appendChild(mark);
                index = pos + query.length;
            }
            node.parentNode.replaceChild(span, node);
        }
    });
});
</script>