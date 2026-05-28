<?php
require_once "config/database.php";

class ProfileController {

    private $pdo;

    public function __construct() {
        $this->pdo = getConnection();
    }

    /**
     * Get user profile details
     */
    public function getProfile($id_pengguna) {
        $stmt = $this->pdo->prepare("
            SELECT id_pengguna, nama_penuh, emel, no_telefon, peranan, tarikh_cipta 
            FROM pengguna 
            WHERE id_pengguna = ?
        ");
        $stmt->execute([(int)$id_pengguna]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update user profile details
     */
    public function updateProfile($id_pengguna, $data) {
        $id = (int)$id_pengguna;
        $nama_penuh = trim($data['nama_penuh'] ?? '');
        $no_telefon = preg_replace('/[^\d+]/', '', trim($data['no_telefon'] ?? ''));

        if (empty($nama_penuh)) {
            return "Nama penuh tidak boleh dibiarkan kosong.";
        }

        // Malaysian phone number validation and normalization (matching AuthController)
        $raw_digits = $no_telefon;
        if (strpos($raw_digits, '+60') === 0) {
            $raw_digits = substr($raw_digits, 3);
        } elseif (strpos($raw_digits, '60') === 0 && strlen($raw_digits) >= 11) {
            $raw_digits = substr($raw_digits, 2);
        } elseif (strpos($raw_digits, '0') === 0) {
            $raw_digits = substr($raw_digits, 1);
        }

        if (!preg_match("/^1\d{8,9}$/", $raw_digits)) {
            return "Format nombor telefon tidak sah.";
        }

        $no_telefon = "+60" . $raw_digits;

        try {
            $stmt = $this->pdo->prepare("
                UPDATE pengguna 
                SET nama_penuh = ?, no_telefon = ? 
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$nama_penuh, $no_telefon, $id]);

            // Update active session metadata
            if ($_SESSION['id_pengguna'] == $id) {
                $_SESSION['nama_penuh'] = $nama_penuh;
            }

            return true;
        } catch (PDOException $e) {
            return "Ralat mengemaskini profil: " . $e->getMessage();
        }
    }

    /**
     * Change user password securely
     */
    public function changePassword($id_pengguna, $data) {
        $id = (int)$id_pengguna;
        $current_pass = $data['kata_laluan_semasa'] ?? '';
        $new_pass = $data['kata_laluan_baru'] ?? '';
        $confirm_pass = $data['kata_laluan_sahkan'] ?? '';

        if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
            return "Sila isi semua medan kata laluan.";
        }

        if (strlen($new_pass) < 8) {
            return "Kata laluan baru mesti sekurang-kurangnya 8 aksara.";
        }

        if ($new_pass !== $confirm_pass) {
            return "Kata laluan baru dan pengesahan kata laluan tidak sepadan.";
        }

        // Retrieve current password hash
        $stmt = $this->pdo->prepare("SELECT kata_laluan_hash FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return "Pengguna tidak ditemui.";
        }

        // Verify current password is correct
        if (!password_verify($current_pass, $user['kata_laluan_hash'])) {
            return "Kata laluan semasa salah.";
        }

        // Hash and save new password
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);

        try {
            $stmt = $this->pdo->prepare("
                UPDATE pengguna 
                SET kata_laluan_hash = ? 
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$new_hash, $id]);
            return true;
        } catch (PDOException $e) {
            return "Ralat menukar kata laluan: " . $e->getMessage();
        }
    }
}
