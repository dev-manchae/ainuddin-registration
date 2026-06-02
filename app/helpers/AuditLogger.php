<?php
// app/helpers/AuditLogger.php
require_once __DIR__ . '/../../config/database.php';

class AuditLogger {

    /**
     * Log an administrative action to the database audit_log table.
     * 
     * @param string $tindakan The high-level action (e.g. "Tukar Status Sesi")
     * @param string|null $butiran Detailed context (e.g. "Sesi ID: 1, Status: T")
     * @return bool
     */
    public static function log($tindakan, $butiran = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_pengguna = $_SESSION['id_pengguna'] ?? 0;
        if (!$id_pengguna) {
            return false;
        }

        try {
            $pdo = getConnection();

            // Fetch the email of the active session user
            $stmt = $pdo->prepare("SELECT emel FROM pengguna WHERE id_pengguna = ?");
            $stmt->execute([$id_pengguna]);
            $emel = $stmt->fetchColumn();

            if (!$emel) {
                $emel = $_SESSION['nama_penuh'] ?? 'Unknown User';
            }

            $stmt = $pdo->prepare("
                INSERT INTO audit_log (id_pengguna, emel_pengguna, tindakan, butiran)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id_pengguna, $emel, $tindakan, $butiran]);
            return true;
        } catch (PDOException $e) {
            error_log("AuditLogger Error: " . $e->getMessage());
            return false;
        }
    }
}
