# Walkthrough - Advanced Administration & Applicant Workflows

We have completed the implementation of all requested features for the administration and applicant workflows:

---

## 1. Rejection Comments & Revision Workflow
*   **Database Integration**: Status code `'08'` (`Perlu Kemaskini` / Revision Required) has been added to the database.
*   **Admin Corrections**: In the admin detail panel ([lihat.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/lihat.php)), admins can now click a new **"Minta Kemaskini"** button. This opens an input area where they enter notes and submit, which updates the application status to `'08'` and logs the comment.
*   **Student Dashboard Notifications**: The student dashboard ([dashboard.php](file:///d:/xampp/htdocs/ainuddin-registration/views/dashboard.php)) now checks if any permohonan requires revisions. If so, it renders a bold amber warning banner displaying the admin's exact comments, date, and a direct link to **"Mula Kemaskini"**.
*   **Wizard Integration**: The router ([index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)) allows resuming applications that are in status `'00'` (Draft) or `'08'` (Revision Required). Resubmissions preserve the student's reference number (`no_rujukan`) and set the status back to `'03'` (Dihantar/Dalam Proses).

---

## 2. Admin Dashboard Analytics
*   **Database Metrics**: Expanded `getStats()` in [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) to pull status counts (including the new `'08'` code) and compute daily registration volumes for the last 7 days.
*   **Visual Charts Grid**: Integrated Chart.js via CDN on [dashboard.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/dashboard.php):
    *   **Taburan Status Permohonan**: A doughnut chart plotting the active distribution of applications styled with modern status color codes.
    *   **Trend Pendaftaran**: A rounded bar chart displaying daily application volumes over the last 7 days.

---

## 3. CSV/Excel Data Export
*   **Unified Export**: Implemented `exportCSV($filters)` in [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) that executes database queries mapping comprehensive student, guardian (Primary and Secondary), academic, and health details into a single flat spreadsheet row.
*   **Excel Alignment**: Writes the UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) at the start of the output buffer to ensure Malay names and special characters open correctly in Microsoft Excel on Windows.
*   **Filtered Export**: In the list view ([senarai.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/senarai.php)), a new **"Eksport CSV"** button filters the download to match active search queries/status filters, allowing admins to export either segmented views or the entire catalog.

---

## 4. Email Notification Simulator
*   **Mail Simulation Service**: Implemented [EmailSimulator.php](file:///d:/xampp/htdocs/ainuddin-registration/app/helpers/EmailSimulator.php) which wraps templates into responsive HTML alerts and logs them in the `simulasi_emel` table.
*   **Automated Triggers**: Logs automatic emails on:
    *   *Submission*: Confirms application receipt and provides the reference number.
    *   *Approval*: Congratulates parents and provides the newly generated official Student Number.
    *   *Revision Requests*: Informs parents of required modifications, showing the admin's notes.
    *   *Rejection*: Informs parents of decision outcomes.
*   **Admin Email Log**: Added a **"Simulasi Emel"** panel in the admin sidebar ([sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php) & [emails.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/emails.php)) allowing administrators to review sent logs. Clicking "Lihat Emel" opens a modal containing an encapsulated iframe rendering the styled HTML output.

---

## 5. Sesi (Tahun) & Date Range Filters
*   **Dynamic Intake Dropdown**: Added a dynamically populated **"Sesi (Tahun)"** select dropdown to the filters bar. It queries distinct registration years from the database so administrators can select specific intake cohorts.
*   **Calendar-pick Date Range**: Integrated two standard HTML5 calendar-pick inputs (**"Tarikh Dari"** and **"Tarikh Hingga"**) to allow precise time range filtering based on the application's creation date (`tarikh_cipta`).
*   **Clean Reset Workflow**: Updated the filter reset button logic to detect any active Sesi or date range selections and clear all filters upon click.
*   **Segmented Exporter**: Linked the new filters to the CSV exporter, ensuring only the filtered subset is exported when clicking the green **"Eksport CSV"** button.

---

## 6. Security Hardening & Session Cookie Securing
*   **Defensive Upload Folder Lock**: Created a defensive `.htaccess` file inside `public/uploads/` to block any potential direct PHP/script execution within the uploads subfolders.
*   **Environment-Independent Connection**: Rewrote the database configuration to use environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) with fallback defaults for local XAMPP setups.
*   **Secure Session Cookie configuration**: Set hardened cookie attributes (`HttpOnly`, `SameSite=Lax`, and `Secure` conditionally active on HTTPS) in `index.php` before beginning user sessions.

---

## 7. Layout & Navigation Alignment Polish
*   **Success Page Cleanup**: Conditionally hid the step progress dots and bottom wizard navigation buttons when rendering the `success.php` template.
*   **Clean Button Grouping**: Grouped action buttons in `registration_layout.php` so "← Kembali" stays on the left, while "Simpan & Keluar" and "Seterusnya →" are side-by-side on the right, correcting footer alignment across all registration steps.

---

## Verification & Manual Testing Steps

### 1. Test Revision & Resubmission Workflow
*   Log in as admin (`admin@gmail.com` / `admin123`) and select an application in "Dihantar" status.
*   Click **"Minta Kemaskini"**, type `Sila muat naik semula gambar IC Pelajar yang lebih jelas.` and click submit.
*   Confirm the status changes to **Perlu Kemaskini**.
*   Log in as the applicant. You will see a notification alert with the revision notes.
*   Click **"Mula Kemaskini"** or **"Kemaskini"**, go to step 5, re-upload the file, proceed to step 6, and submit.
*   Confirm that the status returns to **Dihantar** and retains the same reference number.

### 2. Test Admin Dashboard Charts
*   Log in as admin and verify the Doughnut and Bar charts display correctly on the main dashboard.

### 3. Test CSV Export
*   In the Admin applications list, filter by status or search.
*   Click **"Eksport CSV"**. Open the downloaded CSV file in Excel or Notepad to verify the columns are formatted correctly.

### 4. Test Email Log Simulator
*   Go to **"Simulasi Emel"** in the admin sidebar.
*   Inspect the list and click **"Lihat Emel"** on a log entry to preview the formatted HTML.

### 5. Test Sesi (Tahun) & Date Range Filters
*   Navigate to **"Senarai Permohonan"** in the admin panel.
*   Verify the Sesi dropdown contains the appropriate intake years (e.g. `2026`).
*   Select an intake year or pick a **"Tarikh Dari"** / **"Tarikh Hingga"** range, and click **"Tapis"**.
*   Confirm the applications list displays only records matching your constraints.
*   Click **"Eksport CSV"** while the filters are active and verify that the exported file contains only the matching filtered records.
*   Click **"Reset"** to clear the selections and restore the full list view.
