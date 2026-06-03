# Walkthrough 2.7 - Intake Deadlines, Email Template Editor, and Standalone Profile PDF Export

We have documented the features released in Milestone `v2.7` to ensure that all documentation is completely up to date.

## 1. Key Features

1. **Intake Deadlines Enforcement**:
   * The parent dashboard now queries the active intake session and displays its closing date.
   * If there is no active intake or the current date is past the closing date, the "+ Permohonan Baru" button is disabled.
   * Existing drafts belonging to closed or inactive intakes are marked with a "Tamat Tempoh" (Expired) badge and their resume/edit links are disabled.

2. **Email Template Editor**:
   * Added a web-based editor interface in the admin panel to view and modify transactional templates stored in the `email_templates` database.
   * Supported email keys:
     * `pendaftaran_diterima`: Sent when a parent submits their application.
     * `permohonan_diluluskan`: Sent when the application is approved (includes official student ID).
     * `pembetulan_diperlukan`: Sent when revision is requested by the admin.
     * `permohonan_ditolak`: Sent when the application is rejected.
   * Implemented placeholder tokens (e.g. `{nama_penjaga}`, `{nama_pelajar}`, `{no_rujukan}`, `{no_pelajar}`, `{catatan}`, `{brand}`) to dynamically parse metadata at delivery.
   * Changes are secured with CSRF validation and logged via the system's `AuditLogger`.

3. **Standalone Profile PDF Export**:
   * Introduced [ProfilPelajarGenerator.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/ProfilPelajarGenerator.php), a custom FPDF-based layout engine.
   * Compresses the multi-step registration data (Student details, Family details, Academic results, Health details, and a Checklist of uploaded files) into a clean, single-page A4 print layout.
   * Styled with custom Forest Green margins, structured headers, double divider lines, and font auto-scaling to prevent multiline page overflows.

---

## 2. File Modifiers

* [PermohonanController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/PermohonanController.php) [MODIFY] - Integrated active intake validation rules.
* [EmailSimulator.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/EmailSimulator.php) [MODIFY] - Refactored dynamic templating to load edited records from database instead of static template files.
* [ProfilPelajarGenerator.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/ProfilPelajarGenerator.php) [NEW] - Generated custom single-page FPDF formatting class.
* [email_templates.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/email_templates.php) [NEW] - Added HTML template edit interface with responsive sidebar showing token references.
* [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php) [MODIFY] - Added admin routes for email template edit and save actions.
