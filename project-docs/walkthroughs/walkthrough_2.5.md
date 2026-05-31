# Walkthrough 2.5 - Intake/Batch Management & Step Wizard Auto-Save

We have successfully implemented two major feature milestones:
1. **Intake/Batch Management System**: Admins can now manage intake sessions (e.g. define sessions, set registration opening/closing dates, configure student application limits/quotas, toggle statuses, and delete sessions). If registration is closed or quotas are full, parent users are restricted from starting new applications, and a banner is displayed on their dashboard.
2. **Step Wizard AJAX Auto-Save**: Background draft auto-saving has been integrated across Steps 1 to 5 of the registration form. User keystrokes and selections are captured, debounced by `1200ms`, and sent as AJAX background requests. A modern status indicator overlay informs the user in real-time ("Menyimpan draf...", "Draf disimpan", or warnings).

---

## 1. Feature Details

### Intake/Batch Management System
* **Database Table (`intake_batch`)**: Created the schema tracking the intake's name, dates, quotas, status, and creation timestamps.
* **Migration Script (`setup_intake.php`)**: Automated database setup, seeded a default `Sesi Akademik 2026/2027` active intake, and associated all existing applications in the database with it to maintain consistency.
* **Admin Intake view (`intakes.php`)**: Styled a dual-card view matching the theme. Allows admins to add new intakes, dynamically toggle active status badges, edit existing rows, and check student registrations per session. Deletion is restricted if student records are linked.
* **Registration Availability Enforcement**: Modified draft initialization to block new applications if registrations are closed or the quota is filled.
* **Parent Dashboard Restrictions**: Disabled the "+ Permohonan Baru" action button on the parent dashboard and displayed an HSL-themed warning banner if no active registration session exists.

### Step Wizard AJAX Auto-Save
* **Background Request Listener**: The form actions of Steps 1 to 5 are automatically detected. Changed input fields queue a `1200ms` debounce timer before sending the serialized `FormData` body.
* **Controller AJAX Detection**: Updated the save routes in `index.php` to skip HTTP headers and return structured JSON states (`{success: true}`) when detecting `ajax=1` requests.
* **Visual Status Indicator**: Positioned a smooth-transitioning notification box at the top right of the card. Features status colors mapping the HSL palette: blue (saving), green (success), and red (errors), with a rotating CSS micro-spinner.

---

## 2. File Modifiers

* [setup_intake.php](file:///d:/xampp/htdocs/ainuddin-registration/database/setup_intake.php) [NEW] - Creates the database schema and performs data migration.
* [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) [MODIFY] - Appends get, add, update, status toggle, and delete intake methods.
* [PermohonanController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/PermohonanController.php) [MODIFY] - Integrates getActiveIntake and checks intake availability on draft creation.
* [intakes.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/intakes.php) [NEW] - Administrative intakes management panel template.
* [sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php) [MODIFY] - Registers the "Urus Sesi" sidebar navigation item.
* [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php) [MODIFY] - Adds admin intake routing actions and supports AJAX JSON responses for save steps.
* [dashboard.php](file:///d:/xampp/htdocs/ainuddin-registration/views/dashboard.php) [MODIFY] - Restricts registration button and displays closed alerts based on active intake.
* [registration_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/registration_layout.php) [MODIFY] - Embeds the autosave status indicator div.
* [main.css](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/css/main.css) [MODIFY] - Declares saving indicator classes and rotating keyframe animations.
* [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js) [MODIFY] - Hooks form change listeners, debounces fetches, and manages indicator states.

---

## 3. Verification & Manual Testing Steps

### Syntax Validation
* Checked compile safety on all new/modified files:
  `d:\xampp\php\php.exe -l app/controllers/AdminController.php app/controllers/PermohonanController.php index.php views/admin/intakes.php views/admin/sidebar.php views/layouts/registration_layout.php views/dashboard.php`
  *Result*: No syntax errors detected.

### Manual Verification Flow

#### 1. Intake Session Control & Restrictions
1. Log in to the Admin Portal (`admin@gmail.com` / `admin123`).
2. Click **Urus Sesi** in the sidebar. Verify the default session is listed with a green status badge and "11 orang" applications.
3. Turn off the default intake session by clicking **Tukar Status**.
4. Log out and log in as a parent. Verify the "+ Permohonan Baru" button is greyed out and the warning banner states registration is closed.
5. Go back to the admin portal and **Tukar Status** back to active. Verify registration becomes available again.

#### 2. AJAX Auto-Save Wizard
1. Begin a new parent registration (or resume a draft).
2. On Step 1 (Maklumat Pelajar), edit the student name. Verify a blue `"Menyimpan draf..."` message with a spinner appears in the top right of the card and transitions to a green `"✓ Draf disimpan"` checkmark after a second.
3. Refresh the page to verify changes persist.
