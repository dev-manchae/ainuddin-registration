# Walkthrough 3.0 - Instant Tab Switching & Active Search Highlights

We have implemented new UI/UX enhancements in Milestone `v3.0` to improve ease-of-use and speed for administrators.

## 1. Key Features

1. **Instant Client-Side Tab Switching**:
   * Modified the student application detail view [lihat.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/lihat.php).
   * Converted tabs (Pelajar, Penjaga, Akademik, Kesihatan, Dokumen, Log Status) from full page reloads to client-side toggles.
   * Renders all sections in separate DOM containers, showing and hiding them instantly via a Vanilla JS handler.
   * Preserves scroll state and eliminates server roundtrip delays.

2. **Active Search text Highlighting**:
   * Added dynamic keyword highlighting in [senarai.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/senarai.php) (Application List) and [audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/audit_logs.php) (Audit Logs).
   * Uses a safe DOM `TreeWalker` to scan text nodes for matching keywords without interfering with HTML structure.
   * Wraps occurrences in a clean, soft warm-yellow `<mark>` tag with rounded corners for scannability.

---

## 2. File Modifiers

* [lihat.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/lihat.php) [MODIFY] - Refactored tabs section, wrapped sections in panel classes, and added client-side Javascript controllers.
* [senarai.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/senarai.php) [MODIFY] - Appended keyword tree-walking highlight scripts.
* [audit_logs.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/audit_logs.php) [MODIFY] - Appended keyword tree-walking highlight scripts.

---

## 3. Verification & Testing

### Syntax validation
* Verified compile safety:
  `d:\xampp\php\php.exe -l views/admin/lihat.php views/admin/senarai.php views/admin/audit_logs.php`
  *Result*: **No syntax errors detected.**

### Manual Verification
* **Instant Switching**: Verified that tab selection changes are immediate and scroll is preserved.
* **Keyword Highlighting**: Confirmed keywords are colored in soft yellow when filtered queries are active in logs or list tables.
