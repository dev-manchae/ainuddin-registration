<h2 style="margin-bottom: 5px;">Dashboard Admin</h2>
<p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Ringkasan sistem pendaftaran</p>

<!-- STATS CARDS -->
<div class="stats-grid">

    <div class="stat-card">
        <h3>Jumlah Permohonan</h3>
        <div class="value">
            <?= $stats['jumlah_permohonan']; ?>
        </div>
    </div>

    <?php foreach ($stats['ikut_status'] as $status): ?>

        <div class="stat-card">
            <h3><?= $status['perihal']; ?></h3>
            <div class="value">
                <?= $status['jumlah']; ?>
            </div>
        </div>

    <?php endforeach; ?>

</div>

<!-- RECENT APPLICATIONS -->
<div class="card">

    <h3 style="margin-bottom: 15px;">Permohonan Terkini</h3>

    <table>
        <thead>
            <tr>
                <th>No Rujukan</th>
                <th>Pemohon</th>
                <th>Pelajar</th>
                <th>Status</th>
                <th>Tarikh</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>

            <?php if (empty($stats['terkini'])): ?>

                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b;">
                        Tiada permohonan.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($stats['terkini'] as $row): ?>

                    <tr>
                        <td>
                            <?= $row['no_rujukan'] ?? '-'; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_pemohon']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_pelajar'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php

                            $badgeClass = 'badge-draft';

                            if ($row['kod_status'] == '03') {
                                $badgeClass = 'badge-submitted';
                            } elseif ($row['kod_status'] == '04') {
                                $badgeClass = 'badge-approved';
                            } elseif ($row['kod_status'] == '05') {
                                $badgeClass = 'badge-rejected';
                            }

                            $statusLabel = match($row['kod_status']) {
                                '00' => 'Draf',
                                '03' => 'Dihantar',
                                '04' => 'Diluluskan',
                                '05' => 'Ditolak',
                                default => $row['kod_status']
                            };

                            ?>
                            <span class="badge <?= $badgeClass; ?>">
                                <?= $statusLabel; ?>
                            </span>
                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($row['tarikh_cipta'])); ?>
                        </td>

                        <td>
                            <a href="?page=admin_lihat&id=<?= $row['id_permohonan']; ?>" 
                               class="btn btn-primary">
                                Lihat
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>

</div>

<!-- BY PROGRAM -->
<div class="card">

    <h3 style="margin-bottom: 15px;">Permohonan Mengikut Program</h3>

    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>

            <?php if (empty($stats['ikut_program'])): ?>

                <tr>
                    <td colspan="2" style="text-align: center; color: #64748b;">
                        Tiada data.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($stats['ikut_program'] as $program): ?>

                    <tr>
                        <td><?= htmlspecialchars($program['program']); ?></td>
                        <td><?= $program['jumlah']; ?></td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>

</div>