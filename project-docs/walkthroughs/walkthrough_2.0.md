# Walkthrough 2.0 - User Profile & Security Settings

We have completed the implementation of the **User Profile & Security Settings** module:

---

## 1. Database Schema Update
*   **Failed Attempt Tracking**: Created and ran `database/migrate_lockout.php` to add `failed_logins` (INT, default 0) and `lockout_time` (DATETIME, default NULL) to the `pengguna` table to track brute-force attempts.

---

## 2. Brute-Force Lockout Protection
*   **Login Lockout**: Updated the `login()` methods in both [AuthController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AuthController.php) (for applicants) and [AdminController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AdminController.php) (for administrators):
    *   On 5 consecutive failed login attempts, the account is temporarily locked.
    *   Any login attempt during this period is rejected immediately with a message showing the remaining minutes: `Akaun anda telah disekat sementara. Sila cuba lagi dalam masa X minit.`
    *   Successful logins automatically reset `failed_logins` to `0` and clear the `lockout_time`.

---

## 3. Profile Controller & Settings Page
*   **Profile Logic**: Created [ProfileController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/ProfileController.php) to manage profile details and password updates.
*   **Responsive Profile Card Layout**: Designed a dual-card view [profil.php](file:///d:/xampp/htdocs/ainuddin-registration/views/profile/profil.php) (Personal Details on the left, Password Security on the right):
    *   **Auto-Formatting Phone Number**: Integrated the Malaysian mobile format spacing script (`XX XXX XXXX`) on the phone field.
    *   **Locked Email**: Locked the email field as read-only to preserve user identification consistency.
*   **Role-Adaptive Navigation**:
    *   Updated the parent navigation header ([header.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/header.php)) and the admin sidebar ([sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php)) with "Profil Saya" links.
    *   The router ([index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)) automatically renders the profile page inside the user's role-specific layout (admin sidebar layout vs standard header/footer layout).

---

## 4. Real-time Password Strength Meter
*   Integrated a visual indicator bar below the "New Password" field inside [profil.php](file:///d:/xampp/htdocs/ainuddin-registration/views/profile/profil.php). It uses client-side JavaScript to assess parameters:
    *   🔴 **Weak (Red)**: Password has less than 8 characters, or contains only simple letters.
    *   🟡 **Medium (Yellow)**: Password has 8 or more characters and contains a mix of letters and numbers.
    *   🟢 **Strong (Green)**: Password has 8 or more characters and includes a mix of lowercase/uppercase letters, numbers, and special symbols (e.g. `@`, `$`, `!`).

---

## Verification & Manual Testing Steps

### 1. Test Profile Updates
*   Log in as user (`eunchae@gmail.com` / `eunchae123`) or admin (`admin@gmail.com` / `admin123`).
*   Go to **Profil Saya** in the menu/sidebar.
*   Change your **Nama Penuh** or **Nombor Telefon** (verify the live format spacings as you type). Click **Simpan Perubahan**.
*   Confirm the database updates successfully, and the header name updates immediately.

### 2. Test Secure Password Changes
*   In the **Tukar Kata Laluan** card, try updating your password with an incorrect "Kata Laluan Semasa". Confirm it is rejected.
*   Try typing a weak password (e.g., `abc`) and verify that the strength meter turns red and the system prevents saving with a `< 8 characters` error.
*   Type a strong password containing letters, numbers, capital letters, and symbols (e.g., `Tahfiz@2026`). Confirm the strength bar turns green.
*   Complete a successful password update, log out, and log back in using your new password.

### 3. Test Brute-Force Lockout
*   Go to the login page (`?page=login`).
*   Enter `eunchae@gmail.com` but type a wrong password. Do this **5 times** consecutively.
*   Verify that on the 5th attempt, the system locks the account and displays: `Akaun anda telah disekat sementara selama 15 minit kerana cubaan log masuk gagal yang berlebihan.`
*   Verify that any subsequent attempt (even with the correct password) shows the block warning.
