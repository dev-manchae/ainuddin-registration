<?php require_once "views/admin/sidebar.php"; ?>

<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Ainuddin Registration</title>

    <style>
        :root {
            --primary: #1e5631;
            --primary-dark: #143d23;
            --primary-light: #2e7d32;
            --teal: #00897b;
            --border: #e2e8f0;
            --shadow-sm: 0 2px 8px rgba(30, 86, 49, 0.04);
            --shadow-md: 0 8px 24px rgba(30, 86, 49, 0.08);
            --shadow-lg: 0 16px 40px rgba(30, 86, 49, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f1f5f9;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Base */
        .sidebar {
            width: 260px;
            background: #ffffff;
            color: #1e293b;
            padding: 25px 15px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid #e2e8f0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.02);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 10px 20px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
            white-space: nowrap;
            overflow: hidden;
        }

        .brand-icon {
            min-width: 40px;
            height: 40px;
            background: #f8fafc;
            border-radius: 10px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            border: 1px solid #e2e8f0;
        }

        .brand-text-wrap {
            display: block;
            transition: opacity 0.2s ease, width 0.3s ease;
            overflow: hidden;
        }

        .brand-name {
            font-weight: 700;
            font-size: 18px;
            display: block;
            line-height: 1.2;
        }

        .brand-name small {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: #64748b;
        }

        /* Toggle Button */
        .sidebar-toggle {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            color: #475569;
        }

        .sidebar-toggle:hover {
            background: #e2e8f0;
        }

        .sidebar-toggle svg {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #475569;
            font-weight: 500;
            font-size: 14px;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item:hover {
            background: #f1f5f9;
            color: #1e5631;
        }

        .nav-item.active {
            background: #dcfce7;
            color: #166534;
            font-weight: 600;
        }

        .nav-icon {
            font-size: 12px;
            min-width: 16px;
            text-align: center;
            opacity: 0.6;
        }

        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon {
            opacity: 1;
        }

        .nav-text {
            transition: opacity 0.2s ease, width 0.3s ease;
            display: inline-block;
        }

        .logout {
            color: #b91c1c;
        }

        .logout:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        .sidebar-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 10px 0;
        }

        /* Collapsed State */
        body.sidebar-collapsed .sidebar {
            width: 75px;
            padding: 25px 10px;
        }

        body.sidebar-collapsed .brand-text-wrap,
        body.sidebar-collapsed .nav-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* Flip arrow to point RIGHT when collapsed */
        body.sidebar-collapsed .sidebar-toggle svg {
            transform: scaleX(-1);
        }

        body.sidebar-collapsed .nav-item {
            justify-content: center;
            padding: 10px;
        }

        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding: 0 0 20px;
        }

        /* Main content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            flex: 1;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .main-content {
            margin-left: 75px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        table th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 13px;
            color: #475569;
        }

        table tr:hover {
            background: #f8fafc;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-submitted { background: #dbeafe; color: #1e40af; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary { background: #3b82f6; color: white; }
        .btn-success { background: #22c55e; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-secondary { background: #64748b; color: white; }
        .btn-teal { background: #00897b; color: white !important; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .stat-card h3 {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 12px 15px;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
        }

        .detail-value {
            color: #1e293b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
        }

        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .tabs a {
            padding: 10px 20px;
            text-decoration: none;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: color 0.2s, border-color 0.2s;
        }

        .tabs a.active {
            color: #1e5631;
            border-bottom-color: #1e5631;
            font-weight: 700;
        }
        
        .tabs a:hover:not(.active) {
            color: #334155;
        }

        .preview-image {
            max-width: 200px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        /* ==========================
           DOCUMENT PREVIEW CARDS
           ========================== */
        .doc-preview-card {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px 8px 8px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            max-width: 100%;
            margin-top: 10px;
        }

        .doc-preview-card:hover {
            border-color: #0f766e;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }

        .doc-preview-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 6px;
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .doc-preview-icon.pdf {
            background: #fee2e2;
            color: #ef4444;
        }

        .doc-preview-icon.image {
            background: #ecfdf5;
            color: #10b981;
            overflow: hidden;
        }

        .doc-preview-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doc-preview-icon svg {
            width: 22px;
            height: 22px;
        }

        .doc-preview-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            text-align: left;
        }

        .doc-preview-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .doc-preview-action {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
        }

        /* ==========================
           DIRECT IMAGE PREVIEWS
           ========================== */
        .img-preview-anchor {
            display: inline-block;
            max-width: 100%;
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .img-preview-anchor:hover {
            border-color: #0f766e;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            transform: scale(1.01);
        }

        .img-preview-direct {
            display: block;
            max-width: 250px;
            max-height: 180px;
            object-fit: contain;
            background: #f8fafc;
        }

        .alert {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        @media (max-width: 768px) {
            body.sidebar-collapsed .sidebar {
                width: 0;
                padding: 0;
                border: none;
            }
            
            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }

            .sidebar {
                width: 260px;
            }
            .main-content {
                margin-left: 260px;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="main-content">

        <?php require_once "views/layouts/flash_message.php"; ?>

        <?php require_once $content; ?>

    </div>

    <!-- Load main.js so the sidebar toggle works -->
    <script src="public/assets/js/main.js"></script>

</body>

</html>