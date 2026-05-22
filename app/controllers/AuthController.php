<?php

require_once "config/database.php";

class AuthController {

    private $pdo;

    public function __construct() {

        $this->pdo = getConnection();
    }

    // =========================
    // REGISTER USER
    // =========================
    public function register($data) {

        $nama_penuh = trim($data['nama_penuh']);
        $emel = strtolower(trim($data['emel']));
        $kata_laluan = $data['kata_laluan'];
        $kata_laluan_sahkan = $data['kata_laluan_sahkan'];
        $no_telefon = preg_replace('/\s+/', '', trim($data['no_telefon']));

        if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $emel)) {
            return "Emel mesti menggunakan @gmail.com";
        }

        if (strlen($kata_laluan) < 8) {
            return "Kata laluan mesti sekurang-kurangnya 8 aksara.";
        }

        if ($kata_laluan !== $kata_laluan_sahkan) {
            return "Kata laluan dan pengesahan kata laluan tidak sepadan.";
        }

        if (!preg_match("/^\d{9,10}$/", $no_telefon)) {
            return "Format nombor telefon tidak sah.";
        }

        // Strip existing +60 prefix if present
        $no_telefon = str_replace('+60', '', $no_telefon);

        // Strip leading 0 before prepending +60
        if (substr($no_telefon, 0, 1) == '0') {
            $no_telefon = substr($no_telefon, 1);
        }

        $no_telefon = "+60" . $no_telefon;

        $check = $this->pdo->prepare("
            SELECT id_pengguna 
            FROM pengguna 
            WHERE emel = ?
        ");

        $check->execute([$emel]);

        if ($check->fetch()) {
            return "Emel sudah digunakan.";
        }

        $password_hash = password_hash($kata_laluan, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO pengguna (
                nama_penuh,
                emel,
                kata_laluan_hash,
                no_telefon
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $nama_penuh,
            $emel,
            $password_hash,
            $no_telefon
        ]);

        return true;
    }

    // =========================
    // LOGIN USER
    // =========================
    public function login($data) {

        $emel = strtolower(trim($data['emel']));
        $kata_laluan = $data['kata_laluan'];

        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM pengguna 
            WHERE emel = ?
        ");

        $stmt->execute([$emel]);

        $user = $stmt->fetch();

        if (!$user) {
            return "Emel atau kata laluan salah.";
        }

        if (!password_verify($kata_laluan, $user['kata_laluan_hash'])) {
            return "Emel atau kata laluan salah.";
        }

        $_SESSION['id_pengguna'] = $user['id_pengguna'];
        $_SESSION['nama_penuh'] = $user['nama_penuh'];

        return true;
    }

    // =========================
    // FORGOT PASSWORD (SEND TOKEN)
    // =========================
    public function forgotPassword($data) {
        $emel = strtolower(trim($data['emel']));

        if (empty($emel)) {
            return "Sila masukkan alamat e-mel.";
        }

        $stmt = $this->pdo->prepare("SELECT id_pengguna FROM pengguna WHERE emel = ?");
        $stmt->execute([$emel]);
        $user = $stmt->fetch();

        // Always show success to prevent email enumeration
        if (!$user) {
            return true; 
        }

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expires = time() + 3600; // 1 hour validity

        // Save to DB
        $stmt = $this->pdo->prepare("
            UPDATE pengguna 
            SET reset_token = ?, reset_expires = ? 
            WHERE id_pengguna = ?
        ");
        $stmt->execute([$token, $expires, $user['id_pengguna']]);

        // Send Email (Note: Requires XAMPP Sendmail/MercuryMail configured to actually send)
        $resetLink = "http://localhost/ainuddin-registration/?page=reset_kata_laluan&token=" . $token;
        $subject = "Pautan Set Semula Kata Laluan - Tahfiz Ainuddin";
        $message = "Klik pautan berikut untuk menetapkan semula kata laluan anda:\n\n" . $resetLink;
        $headers = "From: noreply@ainuddin.com\r\nContent-Type: text/plain; charset=UTF-8";

        mail($emel, $subject, $message, $headers);

        return true;
    }

    // =========================
    // RESET PASSWORD (VERIFY TOKEN & UPDATE)
    // =========================
    public function resetPassword($data) {
        $token = trim($data['token']);
        $kata_laluan = $data['kata_laluan'];
        $kata_laluan_sahkan = $data['kata_laluan_sahkan'];

        if (empty($token) || empty($kata_laluan)) {
            return "Pautan tidak sah atau telah luput.";
        }

        if (strlen($kata_laluan) < 8) {
            return "Kata laluan mesti sekurang-kurangnya 8 aksara.";
        }

        if ($kata_laluan !== $kata_laluan_sahkan) {
            return "Kata laluan dan pengesahan kata laluan tidak sepadan.";
        }

        // Find user by token
        $stmt = $this->pdo->prepare("
            SELECT id_pengguna FROM pengguna 
            WHERE reset_token = ? AND reset_expires > ?
        ");
        $stmt->execute([$token, time()]);
        $user = $stmt->fetch();

        if (!$user) {
            return "Pautan tidak sah atau telah luput.";
        }

        // Hash new password
        $password_hash = password_hash($kata_laluan, PASSWORD_DEFAULT);

        // Update password and clear token
        $stmt = $this->pdo->prepare("
            UPDATE pengguna 
            SET kata_laluan_hash = ?, reset_token = NULL, reset_expires = NULL 
            WHERE id_pengguna = ?
        ");
        $stmt->execute([$password_hash, $user['id_pengguna']]);

        return true;
    }

}