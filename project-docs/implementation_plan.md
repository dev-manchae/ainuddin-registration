# Implementation Plan - User Profile & Security Settings

This milestone implements the **User Profile & Security Settings** module. It provides self-contained user configurations (personal details, security parameters, and password adjustments) for both normal applicants and administrators, keeping the core wizard/business flows intact.

## User Review Required

Please review the following design and security decisions:
> [!IMPORTANT]
> * **Lockout Parameters**: We will enforce a **5 failed consecutive login attempts** lockout policy. Once locked out, the account is temporarily disabled for **15 minutes**.
> * **Email Changes**: For data consistency and identifier integrity, the email field will be displayed in the profile but locked (read-only) to prevent accidental log-in conflicts.
> * **Shared Profile Layout**: A unified profile page design is proposed that adapts automatically to the logged-in user's role (rendering the admin sidebar layout for admins, and the standard web header layout for parents).

## Open Questions
* *None at this time.*

## Proposed Changes

### 1. Database Schema Update

#### [NEW] [migrate_lockout.php](file:///d:/xampp/htdocs/ainuddin-registration/database/migrate_lockout.php)
Create a migration script to add security columns to the `pengguna` table:
* `failed_logins` (INT, default 0) to track consecutive failed login attempts.
* `lockout_time` (DATETIME, NULL) to log when the lockout starts.

### 2. Profile Controller Logic

#### [NEW] [ProfileController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/ProfileController.php)
Create a new controller containing:
* `getProfile($id_pengguna)`: Fetches current user details.
* `updateProfile($id_pengguna, $data)`: Validates and updates `nama_penuh` and formatted `no_telefon`.
* `changePassword($id_pengguna, $data)`: Validates the current password, verifies the new password length and matches, hashes the new password using BCRYPT, and updates the database.

#### [MODIFY] [AuthController.php](file:///d:/xampp/htdocs/ainuddin-registration/app/controllers/AuthController.php)
Modify `login($data)` to incorporate the lockout mechanism:
* Check if the account is currently locked out by comparing the current timestamp with `lockout_time + 15 minutes`. If locked, reject the login immediately with a lockout notice.
* If login fails, increment `failed_logins`. If it reaches 5, set `lockout_time` to the current timestamp.
* If login succeeds, reset `failed_logins` and `lockout_time` to 0/NULL.

### 3. Front-End Views

#### [NEW] [profil.php](file:///d:/xampp/htdocs/ainuddin-registration/views/profile/profil.php)
Create the profile settings page view:
* **Maklumat Peribadi Form**: Input fields for Name and Phone Number (with the live formatting script).
* **Tukar Kata Laluan Form**: Input fields for Current Password, New Password, and Confirm Password.
* **Password Strength Indicator**: JavaScript-driven real-time strength bars:
  * **Weak (Red)**: < 8 characters or only letters.
  * **Medium (Yellow)**: >= 8 characters with a mix of letters and numbers.
  * **Strong (Green)**: >= 8 characters with uppercase letters, numbers, and special symbols.
* **Responsive Layout Styling**: Uses clean CSS classes (matching the Poppins design system) to render forms inside a dual-card layout.

#### [MODIFY] [header.php](file:///d:/xampp/htdocs/ainuddin-registration/views/layouts/header.php)
* Insert a link to `Profil Saya` (`?page=profil`) in the user navigation menu.

#### [MODIFY] [sidebar.php](file:///d:/xampp/htdocs/ainuddin-registration/views/admin/sidebar.php)
* Insert a link to `Profil Saya` (`?page=profil`) in the admin navigation sidebar.

### 4. Router Adjustments

#### [MODIFY] [index.php](file:///d:/xampp/htdocs/ainuddin-registration/index.php)
Add routes for profile pages:
* `profil`: Verifies auth session, instantiates `ProfileController`, handles rendering, and processes POST updates for both personal details and password changes.

---

## Verification Plan

### Automated/Syntax Tests
* Validate PHP syntax across all modified and new controller, router, and view files:
  `php -l app/controllers/ProfileController.php views/profile/profil.php index.php`

### Manual Verification
1. **Test Profile Updates**:
   * Navigate to **Profil Saya**. Update your name and phone number.
   * Verify that details are updated instantly in the database and displayed correctly in the header or admin panel.
2. **Test Password Change**:
   * Change your password by typing a mismatching confirmation or incorrect current password. Verify that error alerts display.
   * Change password with a weak password (< 8 chars). Verify it gets rejected.
   * Type a strong password. Confirm the visual strength bar changes to green.
   * Change it successfully and log back in to verify the new password works.
3. **Test Brute-Force Lockout**:
   * Go to the login page and type the wrong password 5 times consecutively.
   * Verify that the system locks you out and shows a message: `Akaun anda telah disekat selama 15 minit kerana cubaan log masuk gagal yang berlebihan.`
   * Wait or mock the lockout time database column, try logging in with the correct password, and verify you are granted access.
