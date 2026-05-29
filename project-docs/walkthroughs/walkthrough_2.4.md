# Walkthrough 2.4 - Inline Lightbox Viewer & Dynamic Consent Management

We have successfully implemented two major administrative enhancements:
1. **Inline Document Lightbox Viewer**: Admins can now preview student photos, IC cards, and certificates directly inside a premium backdrop-blurred overlay modal on [lihat.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/lihat.php) without leaving the page.
2. **Dynamic Consent (Persetujuan) Management**: Built a fully dynamic CRUD dashboard at `?page=admin_persetujuan` using [persetujuan.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/persetujuan.php) allowing admins to create, update, toggle (enable/disable), and delete registration agreements which dynamically sync to Step 6 of the parent registration wizard.

---

## 1. Feature Details

### Inline Document Lightbox Viewer
* Integrated a clean slate-colored overlay modal with a backdrop filter blur effect (`backdrop-filter: blur(8px)`) directly into [lihat.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/lihat.php).
* Injected click event handlers that intercept clicks on student pictures (PNG/JPG) or credentials (PDF) to load them inside an `<img>` tag or encapsulated `<iframe>` natively within the modal wrapper.
* Enabled multiple closing triggers: click on the close button, clicking on the backdrop area, or pressing the `Escape` key.
* Clean resource lifecycle disposal on close (clears iframe and image src tags to free memory).

### Dynamic Consent Management
* **Database Controller logic**: Implemented `getAgreements()`, `addAgreement()`, `updateAgreement()`, `toggleAgreementStatus()`, and `deleteAgreement()` at the bottom of [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php).
* **View Template**: Created [persetujuan.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/persetujuan.php) incorporating:
  * A spacious **stacked layout** (Form Card at the top, Agreements List Card at the bottom) giving the table 100% width and preventing squishing/wrapping of action buttons.
  * A flex layout form where the textarea takes the main space and submit/cancel buttons align nicely on the right (collapsing vertically on mobile).
  * An interactive agreements list table with badges indicating status (Aktif/Tidak Aktif).
  * A dual-purpose card handling both "Tambah Persetujuan" and "Kemaskini Persetujuan". When clicking "Edit" on a row, details populate dynamically using JSON-safe JavaScript bindings.
  * Direct action buttons to delete (with confirmation alerts) and toggle statuses in a single aligned row.
* **Routing Switch-cases**: Registered `admin_persetujuan`, `admin_persetujuan_save`, `admin_persetujuan_toggle`, and `admin_persetujuan_delete` cases inside [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php) complete with session and CSRF protections.
### Layout & Alignment Hotfixes
* **Admin Table Responsive Handling**: Wrapped the table in [senarai.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/senarai.php) with an `overflow-x: auto` card and defined `min-width: 950px` on the table element. Critical data columns (No Rujukan, No KP, Program, Tarikh, Tindakan) are styled with `white-space: nowrap` to prevent text-breaking overlap on smaller screens or browser drag-resizing.
* **Badge Wrap Protection**: Added `white-space: nowrap` to the global `.badge` class in [admin_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/admin_layout.php) to prevent text line wrapping inside badges (e.g. "Perlu Kemaskini").
* **Revision Alert Panel Alignment**: Refactored the alert layout in [dashboard.php](file:///d:/xampp/htdocs/ainuddin-registration/views/dashboard.php) to group the administrator's comments textbox and the "Mula Kemaskini" button into a flex-aligned row matching their heights (`46px`), placing the date label cleanly below.
* **CSS-based Scroll Reveal Animation**: Transitioned the scroll reveal effect from inline JavaScript styling to clean CSS classes. Declared the initial animation state (`opacity: 0; transform: translateY(20px)`) and transitions in [main.css](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/css/main.css) and [admin_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/admin_layout.php). Added the `.revealed` selector to ensure reliable hardware-accelerated animations on all pages (preventing delays caused by loading charts or CDNs).

---

## 2. File Modifiers

* [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) - Added database CRUD methods.
* [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php) - Added routing logic switch-cases.
* [lihat.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/lihat.php) - Injected modal markup, custom styles, and click intercept JS.
* [sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php) - Injected navigation link.
* [persetujuan.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/persetujuan.php) - Created the administrative consent management panel view.
* [admin_layout.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/admin_layout.php) - Injected inline synchronous script block to prevent sidebar toggle flash, and defined scroll reveal transition styles.
* [main.js](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/js/main.js) - Added stats/profile cards to fade-in reveal list and updated to add the `.revealed` class on trigger.
* [main.css](file:///d:/xampp/htdocs/ainuddin-registration/public/assets/css/main.css) - Defined scroll reveal transition rules and states for global cards.

---

## 3. Verification & Manual Testing Steps

### Syntax Validation
* Ran individual syntax linters to confirm compile-safety:
  `d:\xampp\php\php.exe -l app/controllers/AdminController.php index.php views/admin/lihat.php views/admin/sidebar.php views/admin/persetujuan.php`
  *Result*: No syntax errors detected.

### Manual Verification Flow

#### 1. Lightbox Document Previewer
1. Log in to the Admin Portal (`admin@gmail.com` / `admin123`).
2. Go to **Senarai Permohonan** -> Select any application -> Select the **Dokumen** tab.
3. Click on the uploaded student photo (image) or IC card. The lightbox modal opens instantly showing the image with a blurred background. Click on the backdrop or close button to close it.
4. Click on the certificate (PDF). The lightbox modal opens showing the PDF cleanly inside a scrollable frame. Press `ESC` to close.

#### 2. Consent Management CRUD & Sync
1. Click **Urus Persetujuan** in the sidebar.
2. Verify the 3 default clauses exist and have the green "Aktif" badge.
3. In the right form card, type `"Saya dengan ini mengaku bersetuju dengan syarat tambahan MTA."` and click **Tambah Klausa**. Confirm it appears in the table with an active status.
4. Click **Edit** on that new clause. Confirm the text loads into the textarea. Edit the text and click **Kemaskini Klausa**. Verify it updates in the table.
5. Click **Tukar Status**. Confirm the badge changes to "Tidak Aktif" (red). Click **Tukar Status** again to change it back.
6. Open a new incognito window (or log out and register a new student parent). Go to Step 6 of the registration wizard. Verify that only the currently active agreements are listed and required.
7. Go back to the Admin screen, click **Padam** on the newly created agreement. Verify that a confirmation dialog appears, and once accepted, the item is permanently deleted from the database.
