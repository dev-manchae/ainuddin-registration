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

<!-- ANALYTICS SECTION -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="margin-bottom: 0;">
        <h3 style="margin-bottom: 20px; font-size: 15px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Taburan Status Permohonan</h3>
        <div style="position: relative; height: 260px; display: flex; align-items: center; justify-content: center;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <h3 style="margin-bottom: 20px; font-size: 15px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Trend Pendaftaran (7 Hari Terakhir)</h3>
        <div style="position: relative; height: 260px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
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

<!-- CHART.JS INTEGRATION -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
// Extract status distribution data
$labels = [];
$counts = [];
$colors = [];
$badgeColors = [
    '00' => '#94a3b8', // Draft (Grey)
    '01' => '#60a5fa', // Received (Blue)
    '02' => '#f87171', // Rejected (Red)
    '03' => '#3b82f6', // In Process (Blue)
    '04' => '#10b981', // Approved (Green)
    '05' => '#ef4444', // Rejected (Red)
    '06' => '#6b7280', // Terminated (Dark Grey)
    '07' => '#f59e0b', // Suspended (Orange)
    '08' => '#d97706', // Revision required (Amber)
];
foreach ($stats['ikut_status'] as $status) {
    if ($status['jumlah'] > 0) {
        $labels[] = $status['perihal'];
        $counts[] = (int)$status['jumlah'];
        $colors[] = $badgeColors[$status['kod']] ?? '#cbd5e1';
    }
}

// Extract daily trends data
$trendDates = [];
$trendCounts = [];
foreach ($stats['trend_harian'] as $trend) {
    $trendDates[] = $trend['tarikh'];
    $trendCounts[] = (int)$trend['jumlah'];
}
?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Status Doughnut Chart
    const statusLabels = <?= json_encode($labels); ?>;
    const statusCounts = <?= json_encode($counts); ?>;
    const statusColors = <?= json_encode($colors); ?>;

    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors,
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 15,
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 11
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // 2. Trend Bar Chart
    const trendLabels = <?= json_encode($trendDates); ?>;
    const trendCounts = <?= json_encode($trendCounts); ?>;

    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Permohonan',
                data: trendCounts,
                backgroundColor: 'rgba(30, 86, 49, 0.85)',
                borderColor: '#1e5631',
                borderWidth: 1.5,
                borderRadius: 5,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            family: "'Poppins', sans-serif"
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: "'Poppins', sans-serif"
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>