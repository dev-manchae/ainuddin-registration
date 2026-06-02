# Walkthrough 2.6 - Session Warning, Drag-and-Drop, Exit Warnings & Audit Logs

We have completed the implementation of all Milestone `v2.6` features, adding key security, logging, and user experience enhancements to the Tahfiz Ainuddin Registration System:

1. **Active Session Expiry Warning**:
   * A client-side countdown timer in [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js) tracks parent and administrator inactivity.
   * If inactivity exceeds 17 minutes, a blurred backdrop modal pops up with a 3-minute live countdown timer.
   * Clicking **"Kekalkan Sesi"** pings the new `session_ping` gateway route via background fetch, resetting the session lifetime and warning timer without interrupting input progress.
2. **Drag-and-Drop File Uploads & Progress Spinner**:
   * Replaced basic file inputs with premium dashed dropzone elements inside [step5.php](file:///d:/xampp/htdocs/ainuddin-registration/views/registration/step5.php).
   * Handled clicks, drop actions, and drag state changes globally via document event delegation in [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js).
   * While the AJAX auto-save of a dropped/selected file is in progress, the dropzone is overlayed with a circular rotating green CSS spinner and pointer events are disabled.
3. **Auto-Save Pending Exit Alert**:
   * Tracks whether the background auto-save process is active via a flag in [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js).
   * Intercepts tab/page exits via a `beforeunload` event handler if an autosave is currently pending, preventing potential loss of the final keystrokes.
4. **Admin Audit Logging Panel**:
   * Established the `audit_log` database table tracking: timestamp, administrator ID & email, action label, and detailed metadata.
   * Built a static helper class [AuditLogger.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/AuditLogger.php) to streamline log generation.
   * Hooked log operations inside [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) (log on intake changes, agreement clause modifications, and application status approvals/rejections).
   * Created [audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/audit_logs.php) list viewer with a live keyword search form.

---

## 1. File Modifiers

* [setup_audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/database/setup_audit_logs.php) [NEW] - Configures and executes the `audit_log` schema migration.
* [AuditLogger.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/AuditLogger.php) [NEW] - Handles administrative log insertions.
* [audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/audit_logs.php) [NEW] - Dashboard logs search and tabular log view template.
* [sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php) [MODIFY] - Appends the "Log Audit" link to the administrator's navigation menu.
* [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php) [MODIFY] - Registers `admin_audit_logs` and `session_ping` routes.
* [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) [MODIFY] - Injects calls to `AuditLogger::log` and adds `getAuditLogs()` fetcher.
* [step5.php](file:///d:/xampp/htdocs/ainuddin-registration/views/registration/step5.php) [MODIFY] - Embeds styled `.upload-dropzone` layouts.
* [main.css](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/css/main.css) [MODIFY] - Injects drag-and-drop aesthetics and loading state spinner overlays.
* [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js) [MODIFY] - Registers page exit listeners, activity event observers, and drag-and-drop operations.
* [registration_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/registration_layout.php) [MODIFY] - Injects the timeout countdown modal.
* [admin_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/admin_layout.php) [MODIFY] - Injects the timeout countdown modal.

---

## 2. Verification & Testing Steps

### Syntax Validation
* Verified compile-safety:
  `d:\xampp\php\php.exe -l app/helpers/AuditLogger.php views/admin/audit_logs.php index.php app/controllers/AdminController.php views/admin/sidebar.php views/layouts/registration_layout.php views/layouts/admin_layout.php views/registration/step5.php`
  *Result*: No syntax errors detected.

### Manual Verification Flow

#### 1. Audit Logging Check
1. Log in to the Admin Portal.
2. Navigate to **Urus Sesi** and click **Tukar Status** to toggle a session.
3. Click **Log Audit** in the sidebar. Verify that a log entry is listed containing:
   * Action: `Tukar Status Sesi Intake`
   * Details: `Sesi ID: X, Status Baru: Y`
4. Search for keywords (e.g. `Sesi`) in the search bar and verify list filtering.

#### 2. Session Warning Modal Countdown
1. For quick validation testing, temporarily decrease the warning threshold in `main.js` from `17 minutes` to `10 seconds`.
2. Keep the tab idle. Verify that after 10 seconds, the modal overlay opens with a countdown starting at `3:00`.
3. Click **Kekalkan Sesi** and verify that the modal disappears and the timeout timer resets.

#### 3. Drag-and-Drop upload
1. Log in as a Parent, start or resume an application, and go to Step 5.
2. Drag and drop a valid PDF or image file into the dashed dropzone.
3. Verify that the dropzone turns semi-transparent, displays a central loading green spinner, and auto-saves the document to the server.

#### 4. Exit Warning
1. Go to Step 1. Edit a field, and immediately refresh the page or try to close the tab before the `1.2 second` auto-save completes.
2. Confirm the browser interrupts you with a confirmation alert prompt warning of unsaved changes.
