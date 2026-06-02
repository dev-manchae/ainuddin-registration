<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    echo "Connected to database successfully.\n";

    // 1. Create table intake_batch if not exists
    $tableExists = false;
    try {
        $pdo->query("SELECT 1 FROM intake_batch LIMIT 1");
        $tableExists = true;
        echo "Table 'intake_batch' already exists.\n";
    } catch (PDOException $e) {
        // Table does not exist
    }

    if (!$tableExists) {
        echo "Creating 'intake_batch' table...\n";
        $pdo->exec("
            CREATE TABLE `intake_batch` (
                `id_intake` int(11) NOT NULL AUTO_INCREMENT,
                `nama_intake` varchar(100) NOT NULL,
                `tarikh_buka` date NOT NULL,
                `tarikh_tutup` date NOT NULL,
                `had_pelajar` int(11) NOT NULL DEFAULT 0,
                `status` char(1) NOT NULL DEFAULT 'Y',
                `tarikh_cipta` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id_intake`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");
        echo "Table 'intake_batch' created successfully.\n";
    }

    // 2. Seed a default active intake if empty
    $count = $pdo->query("SELECT COUNT(*) FROM intake_batch")->fetchColumn();
    $defaultIntakeId = 0;
    
    if ($count == 0) {
        echo "Seeding default active intake batch...\n";
        $stmt = $pdo->prepare("
            INSERT INTO intake_batch (nama_intake, tarikh_buka, tarikh_tutup, had_pelajar, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'Sesi Akademik 2026/2027',
            '2026-01-01',
            '2026-12-31',
            100,
            'Y'
        ]);
        $defaultIntakeId = (int)$pdo->lastInsertId();
        echo "Default active intake 'Sesi Akademik 2026/2027' created with ID: $defaultIntakeId.\n";
    } else {
        // Retrieve the first active or first available intake
        $defaultIntakeId = (int)$pdo->query("SELECT id_intake FROM intake_batch ORDER BY id_intake ASC LIMIT 1")->fetchColumn();
        echo "Intake batches already exist. Default intake ID to link is: $defaultIntakeId.\n";
    }

    // 3. Update existing permohonan records where id_intake is NULL
    if ($defaultIntakeId > 0) {
        echo "Migrating existing applications to point to default intake...\n";
        $stmt = $pdo->prepare("
            UPDATE permohonan 
            SET id_intake = ? 
            WHERE id_intake IS NULL
        ");
        $stmt->execute([$defaultIntakeId]);
        $affected = $stmt->rowCount();
        echo "Successfully linked $affected existing applications to intake ID: $defaultIntakeId.\n";
    }

    echo "Intake database migration completed successfully!\n";

} catch (Exception $e) {
    die("Error setting up intake table: " . $e->getMessage() . "\n");
}
