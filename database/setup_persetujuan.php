<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    
    // 1. Create table if not exists
    $tableExists = false;
    try {
        $pdo->query("SELECT 1 FROM persetujuan LIMIT 1");
        $tableExists = true;
        echo "Table 'persetujuan' already exists.\n";
    } catch (PDOException $e) {
        // Table does not exist
    }

    if (!$tableExists) {
        echo "Creating 'persetujuan' table...\n";
        $pdo->exec("
            CREATE TABLE `persetujuan` (
                `id_persetujuan` int(11) NOT NULL AUTO_INCREMENT,
                `perihal` text NOT NULL,
                `status` char(1) NOT NULL DEFAULT 'Y',
                PRIMARY KEY (`id_persetujuan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
        echo "Table 'persetujuan' created successfully.\n";
    }

    // 2. Seed table with the 3 default clauses if empty
    $count = $pdo->query("SELECT COUNT(*) FROM persetujuan")->fetchColumn();
    if ($count == 0) {
        echo "Seeding default agreements...\n";
        $clauses = [
            "Saya mengesahkan bahawa semua maklumat yang diberikan adalah benar dan tepat.",
            "Saya bersetuju mematuhi segala Tatatertib Pelajar dan Peraturan Asrama Maahad.",
            "Saya bersetuju mengikuti segala aktiviti dan anjuran Maahad."
        ];

        $stmt = $pdo->prepare("INSERT INTO persetujuan (perihal, status) VALUES (?, 'Y')");
        foreach ($clauses as $clause) {
            $stmt->execute([$clause]);
        }
        echo "Successfully seeded 3 agreements.\n";
    } else {
        echo "Table 'persetujuan' already seeded.\n";
    }

} catch (Exception $e) {
    die("Error setting up persetujuan table: " . $e->getMessage() . "\n");
}
