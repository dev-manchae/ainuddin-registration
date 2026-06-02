# Implementation Plan - Session Warning, Drag-and-Drop Uploads, Auto-Save Alerts, and Audit Logging

We will implement a set of administrative and user experience enhancements for the Tahfiz Ainuddin Registration System (Release `v2.6`):
1. **Active Session Expiry Warning**: Client-side countdown timer alerting parents before their PHP session/CSRF token expires, with a heartbeat button to extend the session.
2. **Drag-and-Drop Uploads & Loading Progress**: Styled dropzones on Step 5 with drag/drop handlers, instant client-side format/size validation, and a visual loading upload spinner.
3. **Auto-Save Pending Exit Alert**: Intercepts tab/page exits if an AJAX background save is currently in progress, preventing data loss.
4. **Admin Audit Logging**: Database logger tracking admin actions (status changes, intake modifications, consent changes) and rendering a logs viewer in the admin portal.

---

## User Review Required

> [!IMPORTANT]
> **Database Execution**: The new admin auditing features require establishing the `audit_log` table by running the new `database/setup_audit_logs.php` script.

---

## Proposed Changes

### Component 1: Database Setup (Audit Logging)
#### [NEW] [setup_audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/database/setup_audit_logs.php)
* Create `audit_log` table:
  * `id_log` INT AUTO_INCREMENT PRIMARY KEY
  * `id_pengguna` INT NOT NULL (Foreign Key to `pengguna`)
  * `emel_pengguna` VARCHAR(100) NOT NULL (To cache who did it even if accounts are modified)
  * `tindakan` VARCHAR(255) NOT NULL (e.g. "Tukar Status Intake")
  * `butiran` TEXT NULL (Specific log metadata, e.g., intake ID or clause changes)
  * `tarikh_cipta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

#### [NEW] [AuditLogger.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/AuditLogger.php)
* Define static class `AuditLogger` with method `log($tindakan, $butiran = null)` to automatically pull the active `$_SESSION['id_pengguna']` and save logs to the database.

---

### Component 2: Audit Logs View & Sidebar Integration
#### [NEW] [audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/audit_logs.php)
* Administrative Audit View:
  * Grid/Card listing all logged actions with a table showing: No., Timestamp, Admin Email, Action, and Details.
  * Search bar to filter by Admin Email or Action keyword.

#### [MODIFY] [sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php)
* Add a navigation link: "Log Audit" (`?page=admin_audit_logs`) visible only to administrators.

#### [MODIFY] [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)
* Register `admin_audit_logs` page route.
* Log all administrative state changes in `AdminController` cases (e.g. intake updates, consent edits, application approvals/rejections).

#### [MODIFY] [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php)
* Injected calls to `AuditLogger::log()` during actions:
  * `addIntake`, `updateIntake`, `toggleIntakeStatus`, `deleteIntake`
  * `addAgreement`, `updateAgreement`, `toggleAgreementStatus`, `deleteAgreement`
  * `kemaskiniStatus` (approval, rejection, revision requests)

---

### Component 3: Drag-and-Drop & Visual Upload Previews
#### [MODIFY] [step5.php](file:///d:/xampp/htdocs/ainuddin-registration/views/registration/step5.php)
* Replace native standard file inputs with a premium styled dropzone box:
  * Dashed border styling, file icon, and prompt text.
  * Dragover, dragleave, and drop event listeners.
* Trigger a circular rotating CSS upload spinner while the AJAX auto-save of the file is occurring.

#### [MODIFY] [main.css](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/css/main.css)
* Add styles for `.upload-dropzone` container (dashed borders, hover transitions, file icon styling, drop indicators).

---

### Component 4: Active Session Expiry Countdowns & Unload Alerts
#### [MODIFY] [registration_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/registration_layout.php) & [admin_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/admin_layout.php)
* Append a hidden session warning modal dialog markup at the bottom of the layouts.
* Add modal buttons: "Kekalkan Sesi" (ping server) and "Log Keluar".

#### [MODIFY] [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)
* Add `session_ping` AJAX route case returning `{ "status": "active" }` to update the user's session cookie life.

#### [MODIFY] [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js)
* Track `isSaving` state when starting/completing background AJAX auto-saves.
* Add `beforeunload` listener that intercepts exits when `isSaving === true`.
* Initialize a 17-minute javascript inactivity timeout timer. If triggered:
  * Show the warning modal with a 3-minute live countdown timer.
  * Clicking "Kekalkan Sesi" calls `session_ping` via fetch, resets the timer, and hides the modal.
  * If the countdown reaches zero, redirect to `?page=logout`.

---

## Verification Plan

### Automated/Syntax Tests
* Lint all modified and new files:
  `d:\xampp\php\php.exe -l app/helpers/AuditLogger.php views/admin/audit_logs.php index.php app/controllers/AdminController.php`

### Manual Verification
1. **Audit Logs Setup**: Run the `database/setup_audit_logs.php` script. Confirm `audit_log` table is created.
2. **Logs Logging Check**: Perform an action as admin (e.g. toggle an intake's status). Open **Log Audit** in the sidebar. Verify the action is logged.
3. **Session Warning Modal**: Set the JavaScript inactivity timer to 10 seconds for testing. Confirm the warning modal appears with a counting-down timer. Click "Kekalkan Sesi" and check that the session remains active.
4. **Drag-and-Drop uploads**: Open Step 5 of the parent registration. Drag a file into the box. Verify that the loading spinner appears, file uploads, and auto-saves.
5. **Exit Warning Alert**: Edit a text field on Step 1, and immediately close the tab or reload. Verify that the browser interrupts you with a warning alert indicating unsaved data.
