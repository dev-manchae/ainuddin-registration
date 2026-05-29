# Task Progress - Lightbox Viewer & Consent Management

- `[x]` **Component 1: Inline Document Lightbox Viewer**
  - `[x]` Append CSS lightbox styles inside a `<style>` block in `views/admin/lihat.php`.
  - `[x]` Add `doc-wrapper-card` classes and the modal HTML structure to `views/admin/lihat.php`.
  - `[x]` Write JavaScript to intercept document link clicks, handle image/iframe rendering, and manage modal open/close states.
- `[x]` **Component 2: Dynamic Consent (Persetujuan) Management**
  - `[x]` Implement CRUD methods (`getAgreements`, `addAgreement`, `updateAgreement`, `toggleAgreementStatus`, `deleteAgreement`) in `AdminController.php`.
  - `[x]` Add routing logic in `index.php` for viewing, saving, toggling, and deleting agreements.
  - `[x]` Inject the "Urus Persetujuan" link in the admin sidebar menu `views/admin/sidebar.php`.
  - `[x]` Create the administrative view template `views/admin/persetujuan.php` with list tables and dynamic forms.
- `[x]` **Component 3: Verification & Walkthrough**
  - `[x]` Run syntax validation check.
  - `[x]` Manually verify the lightbox modal and agreements CRUD in the browser.
  - `[x]` Create the walkthrough documentation of changes.
