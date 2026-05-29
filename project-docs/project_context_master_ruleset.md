# Tahfiz Ainuddin Registration System - Master Ruleset & Project Context

This document is the absolute source of truth for the Tahfiz Ainuddin Registration System codebase. It contains all developmental rules, architectural designs, UI/UX principles, database specifications, security workflows, and historical/current milestones to ensure total continuity in future development sessions.

---

## 1. Project Overview & Tech Stack

### Tech Stack
*   **Backend**: Vanilla PHP (PHP 8.x+ compatibility with PDO for SQL interactions).
*   **Frontend**: HTML5, Vanilla JavaScript, Vanilla CSS (strictly avoiding TailwindCSS or other styling frameworks unless explicitly requested).
*   **Database**: MySQL/MariaDB database with environment-fallback PDO connections.
*   **Third-Party Libraries**: 
    *   **FPDF**: For generating dynamic Surat Tawaran (Admission Letters) PDFs.
    *   **Chart.js (via CDN)**: For admin dashboard status doughnut charts and daily registration trend bar charts.

### Directory Structure
```
ainuddin-registration/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php          # Handle Login, Registration, and Lockouts
│   │   ├── PermohonanController.php    # Handle Application Form Wizard steps
│   │   ├── AdminController.php         # Handle Admin Actions, CSV Export, Charts
│   │   └── ProfileController.php       # Handle Profile Details and Password tukar
│   ├── helpers/
│   │   ├── csrf.php                    # CSRF token generation & verification
│   │   └── EmailSimulator.php          # Template engine and logger for simulated emails
│   └── middleware/
│       ├── AuthMiddleware.php          # Restricts parent pages to active logins
│       └── AdminMiddleware.php         # Restricts admin pages to administrator users
├── config/
│   └── database.php                    # PDO Database connection configurations
├── database/
│   ├── schema.sql                      # SQL database schema
│   └── migrate_lockout.php             # Security migration script adding lockout tracking
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── main.css                # Global stylesheet containing core design rules
│   │   ├── js/
│   │   │   └── main.js                 # Global JavaScript helper scripts
│   │   └── images/                     # System logo and static images
│   └── uploads/
│       ├── gambar/                     # Ignored, placeholder .gitkeep
│       ├── pelajar_ic/                 # Ignored, placeholder .gitkeep
│       ├── sijil/                      # Ignored, placeholder .gitkeep
│       └── .htaccess                   # Defensive file blocking script executions
├── views/
│   ├── admin/                          # Admin portal templates
│   ├── auth/                           # Authentications (login, register, forgot/reset password)
│   ├── layouts/                        # Reusable portal layouts (header, footer, sidebar)
│   ├── profile/                        # User settings views
│   ├── registration/                   # Wizard steps templates (step 1-6 & success)
│   ├── home.php                        # Portal landing page
│   └── dashboard.php                   # Parent dashboard view
├── walkthroughs/                       # Numbered release logs and testing steps
├── .gitignore                          # Excludes system, upload files and dump scripts
└── index.php                           # Application gateway & Router switch-case
```

---

## 2. Core Architectural & Coding Standards

### Backend Architecture (Procedural-MVC Hybrid)
*   **Routing gateway (`index.php`)**: Acts as the single entry point. Page paths are mapped via `?page=name` using a clear `switch-case` block. Redirects and actions must terminate with `exit;` immediately after sending headers.
*   **OOP Controllers**: Controllers reside in `app/controllers/`. They instantiate PDO connections, validate form inputs, interact with models or tables, and return structured responses (e.g., `true` for success, or string messages detailing failures).
*   **Database Interactions**: 
    *   Connections are fetched via `getConnection()`, which queries environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) with fallback parameters for local setups.
    *   **Always** bind parameters with PDO statements to mitigate SQL injection vectors.
*   **PHP Deprecation Safety**: Code must remain warnings-free under PHP 8.2+. Dynamic callbacks or deprecated function uses (like `imagedestroy()` checks or string replacements) must use defensive code structures.

### Directory / File Naming Conventions
*   **Controllers/Classes**: PascalCase (`ProfileController`, `EmailSimulator`).
*   **Variable/Field Naming**: snake_case (`$id_pengguna`, `$no_telefon`, `failed_logins`, `lockout_time`).
*   **View Files**: lowercase snake_case (`resume_permohonan.php`).
*   **Method/Functions**: camelCase (`updateProfile()`, `validateCsrfToken()`).

---

## 3. UI/UX & CSS Standards

### Global Color System (HSL Palette)
*   **Primary Brand Green**: `#1e5631` (Dark forest green) | Hover: `#163d26` | Light shade: `#2e7d32`
*   **Accent Color**: `#d4af37` (Islamic Gold Accent)
*   **Teal Color**: `#00897b` (Secondary button & success state styling)
*   **Muted slate colors**: Text: `#1e293b` | Sub-labels: `#64748b` | Borders: `#e2e8f0`
*   **Backgrounds**: Light base: `#f8faf9` | White cards: `#ffffff`

### Page Headers & Component Sizing
*   **`.student-header`**: Page header containers are styled uniformly as a spacious white card with a subtle drop shadow (`0 2px 8px rgba(0,0,0,0.04)`), rounded borders (`12px`), and dynamic spacing. Inside it:
    *   `h2`: Font weight `600`, size `22px` color `#1e293b`.
    *   `.subtext`: Color `#64748b`, size `14px`, top-margin `4px`.
*   **`.btn-permohonan`**: Main button styling inside header sections:
    *   **Normal**: Forest green `#1e5631`, white text, `10px 22px` padding, `8px` border radius, semi-bold font weight.
    *   **Disabled**: Light grey background `#cbd5e1`, cursor `not-allowed`, title message explaining constraints.

### Responsive Top Navigation Navbar
*   **Dynamic Sizing**: Navbar height is `75px` at the top of the page. Once scrolled past `50px`, JavaScript toggles the `.scrolled` class, shrinking height to `65px` with a drop-shadow transition to save vertical screen space.
*   **Cache-Busting Principle**: Because local browsers heavily cache the CSS stylesheet, the `<link>` tag in `header.php` must append a dynamic timestamp parameter to bypass cache blocks:
    ```html
    <link rel="stylesheet" href="public/assets/css/main.css?v=<?= time(); ?>">
    ```

### Interactive Javascript UI Elements
*   **Malaysian Mobile Auto-Formatter**: Format phone inputs on keystroke to match standard spacing rules (`XX XXX XXXX` / `11 XXXX XXXX` / `3 XXXX XXXX`) and prepend the country code `+60` during controller sanitization.
*   **Real-time Password Strength Meter**: Displays a 3-bar color metric (Red/Lemah $\rightarrow$ Amber/Sederhana $\rightarrow$ Green/Kuat) checking for length $\ge 8$, letter/digit combinations, and casing/special symbols.

---

## 4. Security & Hardening Expectations

*   **Cookie Security**: Session initialization in `index.php` explicitly sets hardened attributes:
    ```php
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    ```
*   **CSRF Enforcement**: Form submissions must match generated session tokens. CSRF helpers are initialized on every page and checked on POST processing.
*   **Brute-Force Lockout Policy**: 
    *   5 consecutive failed login attempts locks user or admin accounts for **15 minutes**.
    *   Lockout records are tracked in the database columns `failed_logins` and `lockout_time`. Successful logins reset these metrics.
*   **Defensive Upload Folder Lock**: To prevent remote code execution vectors, `public/uploads/` includes a defensive `.htaccess` file denying script execution.

---

## 5. Walkthroughs & Git Workflows

### Documentation Conventions
*   Major milestone updates require a dedicated walkthrough markdown file added to the `walkthroughs/` directory (e.g. `walkthrough_1.0.md`, `walkthrough_2.0.md`).
*   Walkthroughs must clearly list database schema changes, modified controllers/views, and detailed verification checklists for manual validation.

### Git Versioning
*   Code refactoring and feature integrations should be structured in small, clean commits.
*   Stable milestone completions (e.g. Milestone 1.0) must be flagged with Git release tags (e.g. `v1.0`) to establish clear roll-back checkpoints.

---

## 6. Current Progress & Roadmap

### Completed Features
1.  **Status Correction Comments (`'08'` / `Perlu Kemaskini`)**: Admins can request corrections on applications. Students see a revision alert container on their dashboard detailing exact comments and can resume drafts immediately.
2.  **Date & Sesi Filters**: Admin view supports dynamic calendar range selections and intake year dropdown filters linked directly to reports.
3.  **UTF-8 BOM CSV Exporter**: Generates structured CSV dumps of applicant profiles that open seamlessly in MS Excel without character encoding issues.
4.  **HTML Email Log Simulator**: Logs automatic submission, revision, approval, and rejection email actions inside the database and displays them via an iframe simulator in the admin sidebar.
5.  **Failed Login Lockouts**: Locked logins on 5 consecutive failures for 15 minutes.
6.  **User Settings / Password Tukar**: Dual-card layout allowing users to update their name, format phone fields, and securely update passwords with strength validations.
7.  **Unified Header Styling**: Normalized the page header layout cards across the dashboard, settings profile, and wizard step sections.

### Future Roadmap / Pending Tasks
*   **Role Management & Senior Review**: Features that require senior developer review or database configurations modification before direct rollout.
*   **Staging Deployments**: Deployment-related checks and verifying asset caching settings on live staging environments.

### Known Caveats / Troubleshooting
*   **Browser Asset Caching**: If changes to the global stylesheet fail to show up, verify that the dynamic query parameters are active on the stylesheet link in `header.php` to force resource invalidation.
