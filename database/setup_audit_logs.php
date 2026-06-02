<?php
// database/setup_audit_logs.php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    
    // Create audit_log table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `audit_log` (
            `id_log` INT AUTO_INCREMENT PRIMARY KEY,
            `id_pengguna` INT NOT NULL,
            `emel_pengguna` VARCHAR(100) NOT NULL,
            `tindakan` VARCHAR(255) NOT NULL,
            `butiran` TEXT NULL,
            `tarikh_cipta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`id_pengguna`),
            INDEX (`tarikh_cipta`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "Table 'audit_log' created or verified successfully.\n";
} catch (PDOException $e) {
    echo "Error setting up audit log table: " . $e->getMessage() . "\n";
}
