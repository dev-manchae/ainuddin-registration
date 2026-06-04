<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="public/assets/images/logo.png" alt="Logo" style="width: 24px; height: 24px; object-fit: contain;">
        </div>
        <div class="brand-text-wrap">
            <span class="brand-name">Ainuddin</span>
            <small>Panel Pentadbir</small>
        </div>
    </div>

    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg id="toggleIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

    <nav class="sidebar-nav">
        <a href="?page=admin_dashboard" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_dashboard' ? 'active' : ''; ?>" data-tooltip="Dashboard">
            <span class="nav-icon">▸</span> <span class="nav-text">Dashboard</span>
        </a>
        <a href="?page=admin_senarai" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_senarai' ? 'active' : ''; ?>" data-tooltip="Senarai Permohonan">
            <span class="nav-icon">▸</span> <span class="nav-text">Senarai Permohonan</span>
        </a>
        <a href="?page=admin_emails" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_emails' ? 'active' : ''; ?>" data-tooltip="Simulasi Emel">
            <span class="nav-icon">▸</span> <span class="nav-text">Simulasi Emel</span>
        </a>
        <a href="?page=admin_email_templates" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_email_templates' ? 'active' : ''; ?>" data-tooltip="Templat Emel">
            <span class="nav-icon">▸</span> <span class="nav-text">Templat Emel</span>
        </a>
        <a href="?page=admin_persetujuan" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_persetujuan' ? 'active' : ''; ?>" data-tooltip="Urus Persetujuan">
            <span class="nav-icon">▸</span> <span class="nav-text">Urus Persetujuan</span>
        </a>
        <a href="?page=admin_intakes" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_intakes' ? 'active' : ''; ?>" data-tooltip="Urus Sesi">
            <span class="nav-icon">▸</span> <span class="nav-text">Urus Sesi</span>
        </a>
        <a href="?page=admin_audit_logs" class="nav-item <?= ($_GET['page'] ?? '') == 'admin_audit_logs' ? 'active' : ''; ?>" data-tooltip="Log Audit">
            <span class="nav-icon">▸</span> <span class="nav-text">Log Audit</span>
        </a>
        <a href="?page=profil" class="nav-item <?= ($_GET['page'] ?? '') == 'profil' ? 'active' : ''; ?>" data-tooltip="Profil Saya">
            <span class="nav-icon">▸</span> <span class="nav-text">Profil Saya</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="?page=home" class="nav-item" data-tooltip="Laman Utama">
            <span class="nav-icon">↩</span> <span class="nav-text">Laman Utama</span>
        </a>
        <a href="?page=admin_logout" class="nav-item logout" data-tooltip="Log Keluar">
            <span class="nav-icon">↪</span> <span class="nav-text">Log Keluar</span>
        </a>
    </nav>
</div>