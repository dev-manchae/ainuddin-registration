# Implementation Plan - Intake/Batch Management & Step Wizard Auto-Save

We will implement two major architectural updates to the Tahfiz Ainuddin Registration System:
1. **Intake/Batch Management System**: Allows admins to define registration sessions, set opening/closing dates, configure student application limits (quotas), toggle intake statuses, and restrict/disable new student registrations if no active intake session exists or quotas are full.
2. **Step Wizard AJAX Auto-Save**: Integrates a background autosaving mechanism on the parent registration form (Steps 1 to 5) using debounced JavaScript `fetch` calls, so progress is saved as a draft without requiring manual submission.

---

## User Review Required

> [!IMPORTANT]
> **Database Execution**: The new `intake_batch` table structure must be established by running `database/setup_intake.php`. This migration will automatically register a default active session (`Sesi Akademik 2026/2027`) and update any existing applications in the database to link to it to keep historical data intact.

---

## Proposed Changes

### Component 1: Database Setup
#### [NEW] [setup_intake.php](file:///d:/xampp/htdocs/ainuddin-registration/database/setup_intake.php)
* Create `intake_batch` table:
  * `id_intake` INT AUTO_INCREMENT PRIMARY KEY
  * `nama_intake` VARCHAR(100) NOT NULL
  * `tarikh_buka` DATE NOT NULL
  * `tarikh_tutup` DATE NOT NULL
  * `had_pelajar` INT DEFAULT 0 (0 for unlimited quota)
  * `status` CHAR(1) NOT NULL DEFAULT 'Y' ('Y' = Aktif, 'T' = Tidak Aktif)
  * `tarikh_cipta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
* Seed a default active intake: `Sesi Akademik 2026/2027` (running from Jan 1st to Dec 31st of the current year, active status, quota: 100).
* Migrate existing applications: run `UPDATE permohonan SET id_intake = {new_seeded_id} WHERE id_intake IS NULL`.

---

### Component 2: Intake Control Panel & Controllers
#### [MODIFY] [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php)
* Add CRUD methods for managing intakes:
  * `getIntakes()`: Returns all intake records.
  * `addIntake($data)`: Validates dates (Start Date $\le$ End Date) and inserts a new session.
  * `updateIntake($id, $data)`: Updates details of an existing session.
  * `toggleIntakeStatus($id)`: Toggles active state between `Y` and `T`.
  * `deleteIntake($id)`: Deletes an intake session. Safety check: prevents deletion if any student applications are already linked to it in `permohonan`.

#### [MODIFY] [PermohonanController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/PermohonanController.php)
* Add `getActiveIntake()`: Query active sessions where `status = 'Y'` and `CURDATE() BETWEEN tarikh_buka AND tarikh_tutup`. If a quota limit `had_pelajar > 0` is set, check it against submitted applications.
* Modify `createDraft($id_pengguna)`: Verify `getActiveIntake()`. If none exists or quota is exceeded, block draft initialization and return `0`. Assign `id_intake` to the new application record.

#### [NEW] [intakes.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/intakes.php)
* Build the Intake Management UI:
  * Stacked layout matching `views/admin/persetujuan.php`.
  * Top: Dual-purpose Card ("Tambah Sesi" / "Kemaskini Sesi").
  * Bottom: Table listing all intakes with active/inactive status badges, application counts, and action buttons (Edit, Tukar Status, Padam).

#### [MODIFY] [sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php)
* Append the sidebar navigation link: "Urus Sesi Pendaftaran" (`?page=admin_intakes`).

#### [MODIFY] [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)
* Register routing switch-cases:
  * `admin_intakes` -> Renders `views/admin/intakes.php` in `admin_layout.php`.
  * `admin_intake_save`, `admin_intake_toggle`, `admin_intake_delete` -> Run controller actions and redirect back with status alerts.
* Secure all new cases with admin middleware check and CSRF protection.

---

### Component 3: Parent Registration Control
#### [MODIFY] [dashboard.php](file:///d:/xampp/htdocs/ainuddin-registration/views/dashboard.php)
* Check `getActiveIntake()`. If closed:
  * Disable the **+ Permohonan Baru** action button.
  * Display a clear slate-themed warning container alerting parent users: *"Pendaftaran pelajar baharu ditutup buat sementara waktu."*

---

### Component 4: AJAX Auto-Save Step Wizard
#### [MODIFY] [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)
* In cases `save_step1` to `save_step5`:
  * Detect `isset($_GET['ajax'])` requests.
  * If true: Skip browser redirect headers. Run the controller save method as a draft.
  * Return JSON response: `{"success": true}` or `{"success": false, "error": "Reason"}` and `exit;`.

#### [MODIFY] [registration_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/registration_layout.php)
* Insert a hidden container inside `.wizard-card` to render the auto-save indicator:
  ```html
  <div id="autosave-status" class="autosave-indicator" style="display:none;"></div>
  ```

#### [MODIFY] [main.css](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/css/main.css)
* Add CSS styling rules for the `.autosave-indicator`:
  * Absolute positioning in the top right of the form card.
  * Rounded background, tiny typography, and color variables mapping saving states (Blue for saving, green for success, red for errors).

#### [MODIFY] [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js)
* Write auto-save background listener:
  * Target forms with ID `stepForm` whose action targets steps 1 to 5.
  * Intercept input and select elements' change/keystroke actions.
  * Implement a `1000ms` debounce timer.
  * Post a serialised `FormData` payload using browser `fetch` to `form.action + '&ajax=1'`.
  * Manage states visually inside the `#autosave-status` indicator container.

---

## Verification Plan

### Automated/Syntax Tests
* Lint all modified PHP controller and layout views using:
  `d:\xampp\php\php.exe -l {filename}`

### Manual Verification
1. **Database Script**: Run `php database/setup_intake.php`. Verify that the script succeeds and seeds the default intake.
2. **Admin Intake Control**: Log in as admin, go to **Urus Sesi Pendaftaran**. Create a new intake, toggle status, and edit details. Confirm changes sync to database.
3. **Registration Restrictions**:
   * Disable/deactivate all intakes in the admin panel. Log in as parent. Confirm "+ Permohonan Baru" is disabled and the warning banner displays.
   * Enable the intake session. Verify registrations can proceed.
4. **AJAX Wizard Auto-Save**:
   * Open the wizard step 1. Type in the student name. Confirm a small `"Menyimpan draf..."` message displays and transforms to `"Draf disimpan"` in green.
   * Refresh page to verify values persist.
5. **CSRF Enforcement**: Verify that AJAX auto-saves include the CSRF token and execute securely.
