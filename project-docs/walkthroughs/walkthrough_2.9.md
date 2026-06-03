# Walkthrough 2.9 - Inactive Session Timeout Fix & Admin UI Alignment

We have completed the implementation of all Milestone `v2.9` changes to resolve the inactive session timeout bug where parent users were not warned or logged out, and align the admin dashboard status labels.

## 1. File Modifiers

* [footer.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/footer.php) [MODIFY]
  * Injected the `#session-timeout-modal` warning modal HTML markup.
  * Wrapped it in an authentication check (`isset($_SESSION['id_pengguna'])`) so it is rendered globally for logged-in parents (e.g. on dashboard and profile views).
* [registration_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/registration_layout.php) [MODIFY]
  * Removed the duplicate/redundant inline modal markup to avoid ID conflicts, as it is now supplied globally by `footer.php`.
* [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js) [MODIFY]
  * Temporarily configured shorter timers (10 seconds warning, 10 seconds countdown) for manual verification.
  * Restored the production timer limits after successful validation (17 minutes idle time warning, followed by a 3-minute/180-second countdown).
* [dashboard.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/dashboard.php) [MODIFY]
  * Aligned status code `08` rendering on the admin dashboard "Permohonan Terkini" table to map to "Perlu Kemaskini" with a `badge-warning` layout class, resolving the UI discrepancy.

---

## 2. Verification & Testing Steps

### Syntax Validation
* Checked compile-safety of modified PHP files:
  `d:\xampp\php\php.exe -l views/layouts/footer.php views/layouts/registration_layout.php views/admin/dashboard.php`
  *Result*: **No syntax errors detected.**

### Inactivity Timeout Verification
* Set the idle warning duration to 10 seconds and countdown to 10 seconds in `main.js`.
* Logged in as parent and verified that on the parent dashboard page, leaving the browser idle for 10 seconds triggered the warning modal correctly (even when tabbed out/running in the background).
* Verified that clicking **"Kekalkan Sesi"** dismissed the modal, pinged the server, and reset the warning timers.
* Verified that letting the warning countdown expire successfully redirected the page to `?page=logout`.
