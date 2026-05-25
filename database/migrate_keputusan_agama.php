<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    
    // 1. Check and add column if not exists
    $columnExists = false;
    try {
        $pdo->query("SELECT keputusan_agama FROM akademik LIMIT 1");
        $columnExists = true;
        echo "Column 'keputusan_agama' already exists. Skipping ALTER TABLE.\n";
    } catch (PDOException $e) {
        // Column does not exist
    }

    if (!$columnExists) {
        echo "Adding 'keputusan_agama' column to 'akademik' table...\n";
        $pdo->exec("ALTER TABLE akademik ADD keputusan_agama TEXT DEFAULT NULL AFTER keputusan_akademik");
        echo "Column added successfully.\n";
    }

    // 2. Fetch and migrate existing rows
    echo "Starting data migration...\n";
    $stmt = $pdo->query("SELECT id_akademik, surah_hafazan FROM akademik");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $migratedCount = 0;

    foreach ($rows as $row) {
        $id = $row['id_akademik'];
        $surahVal = $row['surah_hafazan'];

        if (empty($surahVal)) {
            continue;
        }

        // Try to decode as JSON
        $decoded = json_decode($surahVal, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            if (isset($decoded['surah_hafazan']) || isset($decoded['keputusan_agama'])) {
                $surahText = $decoded['surah_hafazan'] ?? '';
                $agamaData = $decoded['keputusan_agama'] ?? [];

                // Update row with separated columns
                $update = $pdo->prepare("UPDATE akademik SET surah_hafazan = ?, keputusan_agama = ? WHERE id_akademik = ?");
                $update->execute([
                    $surahText,
                    json_encode($agamaData),
                    $id
                ]);
                $migratedCount++;
            }
        }
    }

    echo "Data migration complete! Migrated $migratedCount rows.\n";

} catch (Exception $e) {
    die("Error during migration: " . $e->getMessage() . "\n");
}
