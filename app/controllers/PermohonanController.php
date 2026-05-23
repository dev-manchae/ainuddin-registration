<?php

require_once "config/database.php";

class PermohonanController {

    private $pdo;

    public function __construct() {

        $this->pdo = getConnection();
    }

    // =========================
    // FORMAT PHONE NUMBER
    // =========================
    private function formatPhoneNumber($number) {

        $number = preg_replace('/\s+/', '', $number);

        $number = str_replace('+60', '', $number);

        if (substr($number, 0, 1) == '0') {

            $number = substr($number, 1);
        }

        return '+60' . $number;
    }

    // =========================
    // VALIDATE PERMOHONAN EXISTS
    // =========================
    private function validatePermohonan($id_permohonan) {

        $id = (int) $id_permohonan;

        if ($id <= 0) {
            return "ID permohonan tidak sah. Sila mula permohonan baharu.";
        }

        $stmt = $this->pdo->prepare("
            SELECT id_permohonan FROM permohonan WHERE id_permohonan = ?
        ");
        $stmt->execute([$id]);

        if (!$stmt->fetch()) {
            return "Permohonan tidak dijumpai. Sila mula permohonan baharu.";
        }

        return true;
    }

    // =========================
    // CREATE DRAFT
    // =========================
    public function createDraft($id_pengguna) {

        $check = $this->pdo->prepare("
            SELECT id_permohonan
            FROM permohonan
            WHERE id_pengguna = ?
            AND kod_status = '00'
            LIMIT 1
        ");

        $check->execute([$id_pengguna]);

        $existing = $check->fetch();

        if ($existing) {

            $verify = $this->pdo->prepare("
                SELECT id_permohonan FROM permohonan WHERE id_permohonan = ?
            ");
            $verify->execute([$existing['id_permohonan']]);
            if ($verify->fetch()) {
                return (int) $existing['id_permohonan'];
            }
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO permohonan (
                id_pengguna,
                kod_status,
                langkah_semasa
            )
            VALUES (?, '00', 1)
        ");

        $stmt->execute([$id_pengguna]);

        $newId = (int) $this->pdo->lastInsertId();

        $verify = $this->pdo->prepare("
            SELECT id_permohonan FROM permohonan WHERE id_permohonan = ?
        ");
        $verify->execute([$newId]);
        if (!$verify->fetch()) {
            return 0;
        }

        return $newId;
    }

    // =========================
    // DELETE DRAFT (SAFE DELETE)
    // =========================
    public function deleteDraft($id_permohonan, $id_pengguna) {
        
        $id = (int) $id_permohonan;

        $stmt = $this->pdo->prepare("
            SELECT id_permohonan, kod_status 
            FROM permohonan 
            WHERE id_permohonan = ? AND id_pengguna = ?
        ");
        $stmt->execute([$id, $id_pengguna]);
        $app = $stmt->fetch();

        if (!$app) {
            return "Permohonan tidak dijumpai.";
        }

        if ($app['kod_status'] !== '00') {
            return "Hanya permohonan draf (belum dihantar) boleh dipadam.";
        }

        $relatedTables = ['dokumen', 'kesihatan', 'akademik', 'keluarga', 'pelajar'];
        foreach ($relatedTables as $table) {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id_permohonan = ?");
            $stmt->execute([$id]);
        }

        $stmt = $this->pdo->prepare("DELETE FROM permohonan WHERE id_permohonan = ?");
        $stmt->execute([$id]);

        return true;
    }

    // =========================
    // GET LOOKUP TABLES
    // =========================
    public function getNegeri() {
        $stmt = $this->pdo->query("SELECT * FROM kod_negeri ORDER BY negeri ASC");
        return $stmt->fetchAll();
    }

    public function getCawangan() {
        $stmt = $this->pdo->query("SELECT * FROM kod_cawangan ORDER BY cawangan ASC");
        return $stmt->fetchAll();
    }

    public function getProgram() {
        $stmt = $this->pdo->query("SELECT * FROM kod_program ORDER BY program ASC");
        return $stmt->fetchAll();
    }

    // =========================
    // SAVE PELAJAR (FIXED: $id position)
    // =========================
    public function savePelajar($data, $id_permohonan, $is_draft = false) {

        $validation = $this->validatePermohonan($id_permohonan);
        if ($validation !== true) {
            return $validation;
        }

        if (!$is_draft) {
            $compulsoryFields = [
                'nama_penuh' => 'Nama Penuh',
                'jantina' => 'Jantina',
                'no_kp' => 'No. Kad Pengenalan',
                'tarikh_lahir' => 'Tarikh Lahir',
                'tempat_lahir' => 'Tempat Lahir',
                'warganegara' => 'Warganegara',
                'alamat' => 'Alamat Penuh',
                'kod_negeri' => 'Negeri',
                'kod_cawangan' => 'Cawangan',
                'kod_program' => 'Program'
            ];
            
            foreach ($compulsoryFields as $field => $label) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    return "Medan '$label' adalah wajib.";
                }
            }

            if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $data['no_kp'])) {
                return "Format No KP tidak sah. Contoh: 041231-08-1234";
            }
        }

        $id = (int) $id_permohonan;

        $check = $this->pdo->prepare("SELECT id_pelajar FROM pelajar WHERE id_permohonan = ?");
        $check->execute([$id]);
        $existing = $check->fetch();

        $fields = [
            'nama_penuh', 'jantina', 'no_kp', 'tarikh_lahir', 
            'tempat_lahir', 'warganegara', 'alamat', 
            'kod_negeri', 'kod_cawangan', 'kod_program'
        ];

        if ($existing) {
            $sql = "UPDATE pelajar SET " . implode(' = ?, ', $fields) . " = ? WHERE id_permohonan = ?";
        } else {
            $sql = "INSERT INTO pelajar (id_permohonan, " . implode(', ', $fields) . ") VALUES (?" . str_repeat(", ?", count($fields)) . ")";
        }

        $values = [];

        // INSERT: id goes FIRST. UPDATE: id goes LAST (WHERE clause)
        if (!$existing) {
            $values[] = $id;
        }

        foreach ($fields as $field) {
            $values[] = isset($data[$field]) && trim($data[$field]) !== '' ? $data[$field] : null;
        }

        if ($existing) {
            $values[] = $id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        if (!$is_draft) {
            $stmt = $this->pdo->prepare("SELECT langkah_semasa FROM permohonan WHERE id_permohonan = ?");
            $stmt->execute([$id]);
            $currentLangkah = (int)$stmt->fetchColumn();
            if ($currentLangkah < 2) {
                $this->pdo->prepare("UPDATE permohonan SET langkah_semasa = 2 WHERE id_permohonan = ?")->execute([$id]);
            }
        }
        return true;
    }

    public function getPelajar($id_permohonan) {
        $stmt = $this->pdo->prepare("SELECT * FROM pelajar WHERE id_permohonan = ?");
        $stmt->execute([(int) $id_permohonan]);
        return $stmt->fetch();
    }

    // =========================
    // SAVE KELUARGA
    // =========================
    public function saveKeluarga($data, $id_permohonan, $is_draft = false) {

        $validation = $this->validatePermohonan($id_permohonan);
        if ($validation !== true) {
            return $validation;
        }

        if (!$is_draft) {
            $compulsory = [
                'nama_bapa' => 'Nama Bapa',
                'telefon_bapa' => 'No. Telefon Bapa',
                'pekerjaan_bapa' => 'Pekerjaan Bapa',
                'pendapatan_bapa' => 'Pendapatan Bapa',
                'alamat_bapa' => 'Alamat Bapa',
                
                'nama_ibu' => 'Nama Ibu',
                'telefon_ibu' => 'No. Telefon Ibu',
                'pekerjaan_ibu' => 'Pekerjaan Ibu',
                'pendapatan_ibu' => 'Pendapatan Ibu',
                'alamat_ibu' => 'Alamat Ibu'
            ];
            
            foreach ($compulsory as $field => $label) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    return "Medan '$label' adalah wajib.";
                }
            }
        }

        $id = (int) $id_permohonan;

        $this->pdo->prepare("DELETE FROM keluarga WHERE id_permohonan = ?")->execute([$id]);

        $penjagaList = [
            ['jenis' => 'Bapa', 'prefix' => 'bapa'],
            ['jenis' => 'Ibu', 'prefix' => 'ibu']
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO keluarga (id_permohonan, jenis_penjaga, nama_penuh, no_telefon, pekerjaan, pendapatan, alamat, emel)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($penjagaList as $p) {
            $prefix = $p['prefix'];
            $namaVal = isset($data['nama_' . $prefix]) && trim($data['nama_' . $prefix]) !== '' ? $data['nama_' . $prefix] : null;
            $telVal = isset($data['telefon_' . $prefix]) && trim($data['telefon_' . $prefix]) !== '' ? $this->formatPhoneNumber($data['telefon_' . $prefix]) : '+60';
            $pekVal = isset($data['pekerjaan_' . $prefix]) && trim($data['pekerjaan_' . $prefix]) !== '' ? $data['pekerjaan_' . $prefix] : null;
            $penVal = isset($data['pendapatan_' . $prefix]) && trim($data['pendapatan_' . $prefix]) !== '' ? $data['pendapatan_' . $prefix] : null;
            $alaVal = isset($data['alamat_' . $prefix]) && trim($data['alamat_' . $prefix]) !== '' ? $data['alamat_' . $prefix] : null;
            $emlVal = isset($data['emel_' . $prefix]) && trim($data['emel_' . $prefix]) !== '' ? $data['emel_' . $prefix] : null;

            $stmt->execute([
                $id,
                $p['jenis'],
                $namaVal,
                $telVal,
                $pekVal,
                $penVal,
                $alaVal,
                $emlVal
            ]);
        }

        if (!$is_draft) {
            $stmt = $this->pdo->prepare("SELECT langkah_semasa FROM permohonan WHERE id_permohonan = ?");
            $stmt->execute([$id]);
            $currentLangkah = (int)$stmt->fetchColumn();
            if ($currentLangkah < 3) {
                $this->pdo->prepare("UPDATE permohonan SET langkah_semasa = 3 WHERE id_permohonan = ?")->execute([$id]);
            }
        }
        return true;
    }

    public function getKeluarga($id_permohonan) {
        $stmt = $this->pdo->prepare("SELECT * FROM keluarga WHERE id_permohonan = ?");
        $stmt->execute([(int) $id_permohonan]);
        $result = $stmt->fetchAll();
        $keluarga = [];
        foreach ($result as $row) {
            $keluarga[$row['jenis_penjaga']] = $row;
        }
        return $keluarga;
    }

    // =========================
    // SAVE AKADEMIK
    // =========================
    public function saveAkademik($data, $id_permohonan, $is_draft = false) {

        $validation = $this->validatePermohonan($id_permohonan);
        if ($validation !== true) {
            return $validation;
        }

        if (!$is_draft) {
            if (!isset($data['nama_sekolah']) || trim($data['nama_sekolah']) === '') {
                return "Nama Sekolah Terdahulu adalah wajib.";
            }
            if (!isset($data['tahap_quran']) || trim($data['tahap_quran']) === '') {
                return "Tahap Penguasaan Al-Quran adalah wajib.";
            }
            if (!isset($data['status_khatam']) || trim($data['status_khatam']) === '') {
                return "Status Khatam adalah wajib.";
            }
        }

        $id = (int) $id_permohonan;

        $akademikJson = [];
        if (isset($data['subjek_akademik'])) {
            foreach ($data['subjek_akademik'] as $i => $subjek) {
                if (!empty(trim($subjek))) {
                    $akademikJson[] = [
                        'subjek' => trim($subjek),
                        'keputusan' => trim($data['keputusan_akademik'][$i] ?? '')
                    ];
                }
            }
        }

        $agamaJson = [];
        if (isset($data['subjek_agama'])) {
            foreach ($data['subjek_agama'] as $i => $subjek) {
                if (!empty(trim($subjek))) {
                    $agamaJson[] = [
                        'subjek' => trim($subjek),
                        'keputusan' => trim($data['keputusan_agama'][$i] ?? '')
                    ];
                }
            }
        }

        $keputusanAgamaVal = json_encode($agamaJson);
        $surahHafazanVal = $data['surah_hafazan'] ?? '';

        $check = $this->pdo->prepare("SELECT id_akademik FROM akademik WHERE id_permohonan = ?");
        $check->execute([$id]);
        $existing = $check->fetch();

        $sekolahVal = isset($data['nama_sekolah']) && trim($data['nama_sekolah']) !== '' ? $data['nama_sekolah'] : null;
        $tahapVal = isset($data['tahap_quran']) && trim($data['tahap_quran']) !== '' ? $data['tahap_quran'] : null;
        $khatamVal = isset($data['status_khatam']) && trim($data['status_khatam']) !== '' ? $data['status_khatam'] : null;

        if ($existing) {
            $stmt = $this->pdo->prepare("
                UPDATE akademik SET
                    nama_sekolah = ?,
                    keputusan_akademik = ?,
                    keputusan_agama = ?,
                    tahap_quran = ?,
                    surah_hafazan = ?,
                    status_khatam = ?
                WHERE id_permohonan = ?
            ");
            $stmt->execute([
                $sekolahVal,
                json_encode($akademikJson),
                $keputusanAgamaVal,
                $tahapVal,
                $surahHafazanVal,
                $khatamVal,
                $id
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO akademik (id_permohonan, nama_sekolah, keputusan_akademik, keputusan_agama, tahap_quran, surah_hafazan, status_khatam)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $id,
                $sekolahVal,
                json_encode($akademikJson),
                $keputusanAgamaVal,
                $tahapVal,
                $surahHafazanVal,
                $khatamVal
            ]);
        }

        if (!$is_draft) {
            $stmt = $this->pdo->prepare("SELECT langkah_semasa FROM permohonan WHERE id_permohonan = ?");
            $stmt->execute([$id]);
            $currentLangkah = (int)$stmt->fetchColumn();
            if ($currentLangkah < 4) {
                $this->pdo->prepare("UPDATE permohonan SET langkah_semasa = 4 WHERE id_permohonan = ?")->execute([$id]);
            }
        }
        return true;
    }

    public function getAkademik($id_permohonan) {
        $stmt = $this->pdo->prepare("SELECT * FROM akademik WHERE id_permohonan = ?");
        $stmt->execute([(int) $id_permohonan]);
        return $stmt->fetch();
    }

    // =========================
    // SAVE KESIHATAN (FIXED: $id position)
    // =========================
    public function saveKesihatan($data, $id_permohonan, $is_draft = false) {

        $validation = $this->validatePermohonan($id_permohonan);
        if ($validation !== true) {
            return $validation;
        }

        $id = (int) $id_permohonan;

        $check = $this->pdo->prepare("SELECT id_kesihatan FROM kesihatan WHERE id_permohonan = ?");
        $check->execute([$id]);
        $existing = $check->fetch();

        // Process fields mapping "Tiada" toggle cleanly
        $mappedData = [
            'alahan' => ($data['alahan_toggle'] ?? '') === 'Tiada' ? 'Tiada' : ($data['alahan'] ?? null),
            'penyakit_kronik' => ($data['penyakit_toggle'] ?? '') === 'Tiada' ? 'Tiada' : ($data['penyakit_kronik'] ?? null),
            'pengambilan_ubat' => ($data['ubat_toggle'] ?? '') === 'Tiada' ? 'Tiada' : ($data['pengambilan_ubat'] ?? null),
            'nombor_kecemasan' => isset($data['nombor_kecemasan']) && trim($data['nombor_kecemasan']) !== '' ? $this->formatPhoneNumber($data['nombor_kecemasan']) : '+60',
            'kebenaran_rawatan' => isset($data['kebenaran_rawatan']) && trim($data['kebenaran_rawatan']) !== '' ? $data['kebenaran_rawatan'] : null
        ];

        $fields = ['alahan', 'penyakit_kronik', 'pengambilan_ubat', 'nombor_kecemasan', 'kebenaran_rawatan'];

        if ($existing) {
            $sql = "UPDATE kesihatan SET " . implode(' = ?, ', $fields) . " = ? WHERE id_permohonan = ?";
        } else {
            $sql = "INSERT INTO kesihatan (id_permohonan, " . implode(', ', $fields) . ") VALUES (?" . str_repeat(", ?", count($fields)) . ")";
        }

        $values = [];

        // INSERT: id goes FIRST. UPDATE: id goes LAST (WHERE clause)
        if (!$existing) {
            $values[] = $id;
        }

        foreach ($fields as $field) {
            $values[] = $mappedData[$field];
        }

        if ($existing) {
            $values[] = $id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        if (!$is_draft) {
            $stmt = $this->pdo->prepare("SELECT langkah_semasa FROM permohonan WHERE id_permohonan = ?");
            $stmt->execute([$id]);
            $currentLangkah = (int)$stmt->fetchColumn();
            if ($currentLangkah < 5) {
                $this->pdo->prepare("UPDATE permohonan SET langkah_semasa = 5 WHERE id_permohonan = ?")->execute([$id]);
            }
        }
        return true;
    }

    public function getKesihatan($id_permohonan) {
        $stmt = $this->pdo->prepare("SELECT * FROM kesihatan WHERE id_permohonan = ?");
        $stmt->execute([(int) $id_permohonan]);
        return $stmt->fetch();
    }

    // =========================
    // SAVE DOKUMEN (UPDATED: allows multiple certificates & safe singular replacements)
    // =========================
    public function saveDokumen($files, $id_permohonan, $is_draft = false) {

        $validation = $this->validatePermohonan($id_permohonan);
        if ($validation !== true) {
            return $validation;
        }

        $id = (int) $id_permohonan;
        $maxSize = 2 * 1024 * 1024;

        // Process singular uploads: IC Pelajar and Gambar Pelajar
        $singularUploads = [
            'ic_pelajar' => [
                'folder' => 'public/uploads/pelajar_ic/', 
                'jenis' => 'IC Pelajar', 
                'allowed' => ['pdf', 'jpg', 'jpeg', 'png']
            ],
            'gambar_pelajar' => [
                'folder' => 'public/uploads/gambar/', 
                'jenis' => 'Gambar Pelajar', 
                'allowed' => ['jpg', 'jpeg', 'png']
            ]
        ];

        foreach ($singularUploads as $inputName => $config) {
            if (isset($files[$inputName]) && $files[$inputName]['error'] == 0) {
                $file = $files[$inputName];
                $originalName = $file['name'];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                $allowed = $config['allowed'];
                if (!in_array($extension, $allowed)) {
                    return "Format fail '$originalName' tidak dibenarkan.";
                }
                if ($file['size'] > $maxSize) {
                    return "Fail '$originalName' melebihi had 2MB.";
                }

                $newFilename = time() . "_" . uniqid() . "." . $extension;
                move_uploaded_file($file['tmp_name'], $config['folder'] . $newFilename);

                // Delete old file record (and physical file)
                $old = $this->pdo->prepare("SELECT nama_fail FROM dokumen WHERE id_permohonan = ? AND jenis_dokumen = ?");
                $old->execute([$id, $config['jenis']]);
                $oldFiles = $old->fetchAll(PDO::FETCH_COLUMN);
                foreach ($oldFiles as $oldFile) {
                    if ($oldFile && file_exists($config['folder'] . $oldFile)) {
                        unlink($config['folder'] . $oldFile);
                    }
                }

                $this->pdo->prepare("DELETE FROM dokumen WHERE id_permohonan = ? AND jenis_dokumen = ?")
                    ->execute([$id, $config['jenis']]);

                $this->pdo->prepare("INSERT INTO dokumen (id_permohonan, jenis_dokumen, nama_fail, nama_asal) VALUES (?, ?, ?, ?)")
                    ->execute([$id, $config['jenis'], $newFilename, $originalName]);
            }
        }

        // Handle sijil_pelajar (multiple array upload)
        if (isset($files['sijil_pelajar'])) {
            $sijilConfig = [
                'folder' => 'public/uploads/sijil/', 
                'jenis' => 'Sijil Pelajar', 
                'allowed' => ['pdf', 'jpg', 'jpeg', 'png']
            ];

            if (is_array($files['sijil_pelajar']['name'])) {
                $fileCount = count($files['sijil_pelajar']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($files['sijil_pelajar']['error'][$i] == 0) {
                        $originalName = $files['sijil_pelajar']['name'][$i];
                        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                        if (!in_array($extension, $sijilConfig['allowed'])) {
                            return "Format fail '$originalName' tidak dibenarkan.";
                        }
                        if ($files['sijil_pelajar']['size'][$i] > $maxSize) {
                            return "Fail '$originalName' melebihi had 2MB.";
                        }

                        $newFilename = time() . "_" . uniqid() . "." . $extension;
                        if (move_uploaded_file($files['sijil_pelajar']['tmp_name'][$i], $sijilConfig['folder'] . $newFilename)) {
                            $this->pdo->prepare("INSERT INTO dokumen (id_permohonan, jenis_dokumen, nama_fail, nama_asal) VALUES (?, ?, ?, ?)")
                                ->execute([$id, $sijilConfig['jenis'], $newFilename, $originalName]);
                        }
                    }
                }
            } else {
                if ($files['sijil_pelajar']['error'] == 0) {
                    $file = $files['sijil_pelajar'];
                    $originalName = $file['name'];
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    if (!in_array($extension, $sijilConfig['allowed'])) {
                        return "Format fail '$originalName' tidak dibenarkan.";
                    }
                    if ($file['size'] > $maxSize) {
                        return "Fail '$originalName' melebihi had 2MB.";
                    }

                    $newFilename = time() . "_" . uniqid() . "." . $extension;
                    if (move_uploaded_file($file['tmp_name'], $sijilConfig['folder'] . $newFilename)) {
                        $this->pdo->prepare("INSERT INTO dokumen (id_permohonan, jenis_dokumen, nama_fail, nama_asal) VALUES (?, ?, ?, ?)")
                            ->execute([$id, $sijilConfig['jenis'], $newFilename, $originalName]);
                    }
                }
            }
        }

        // After processing, check if all three mandatory documents exist
        $check = $this->pdo->prepare("SELECT jenis_dokumen FROM dokumen WHERE id_permohonan = ?");
        $check->execute([$id]);
        $existingTypes = $check->fetchAll(PDO::FETCH_COLUMN);

        $requiredTypes = ['IC Pelajar', 'Gambar Pelajar', 'Sijil Pelajar'];
        $missing = array_diff($requiredTypes, $existingTypes);

        if (!$is_draft) {
            if (!empty($missing)) {
                return "Sila muat naik semua dokumen mandatori yang diperlukan: " . implode(', ', $missing) . ".";
            }
            $stmt = $this->pdo->prepare("SELECT langkah_semasa FROM permohonan WHERE id_permohonan = ?");
            $stmt->execute([$id]);
            $currentLangkah = (int)$stmt->fetchColumn();
            if ($currentLangkah < 6) {
                $this->pdo->prepare("UPDATE permohonan SET langkah_semasa = 6 WHERE id_permohonan = ?")->execute([$id]);
            }
        }
        return true;
    }

    public function getDokumen($id_permohonan) {
        $stmt = $this->pdo->prepare("SELECT * FROM dokumen WHERE id_permohonan = ?");
        $stmt->execute([(int) $id_permohonan]);
        $result = $stmt->fetchAll();
        $dokumen = [];
        foreach ($result as $row) {
            $dokumen[$row['jenis_dokumen']][] = $row;
        }
        return $dokumen;
    }

    public function deleteDokumenById($id_dokumen, $id_permohonan) {
        $id_dokumen = (int)$id_dokumen;
        $id_permohonan = (int)$id_permohonan;

        $stmt = $this->pdo->prepare("SELECT nama_fail, jenis_dokumen FROM dokumen WHERE id_dokumen = ? AND id_permohonan = ?");
        $stmt->execute([$id_dokumen, $id_permohonan]);
        $doc = $stmt->fetch();

        if (!$doc) {
            return "Dokumen tidak dijumpai atau anda tidak mempunyai akses.";
        }

        $folder = '';
        if ($doc['jenis_dokumen'] == 'IC Pelajar') {
            $folder = 'public/uploads/pelajar_ic/';
        } elseif ($doc['jenis_dokumen'] == 'Gambar Pelajar') {
            $folder = 'public/uploads/gambar/';
        } elseif ($doc['jenis_dokumen'] == 'Sijil Pelajar') {
            $folder = 'public/uploads/sijil/';
        }

        if ($folder && file_exists($folder . $doc['nama_fail'])) {
            unlink($folder . $doc['nama_fail']);
        }

        $stmt = $this->pdo->prepare("DELETE FROM dokumen WHERE id_dokumen = ? AND id_permohonan = ?");
        $stmt->execute([$id_dokumen, $id_permohonan]);

        return true;
    }

    // =========================
    // HANTAR PERMOHONAN
    // =========================
    private function generateNoRujukan() {
        $year = date('Y');
        $stmt = $this->pdo->prepare("SELECT nilai_terakhir FROM id_sequel WHERE tahun = ?");
        $stmt->execute([$year]);
        $existing = $stmt->fetch();

        if ($existing) {
            $running = $existing['nilai_terakhir'] + 1;
            $this->pdo->prepare("UPDATE id_sequel SET nilai_terakhir = ? WHERE tahun = ?")->execute([$running, $year]);
        } else {
            $running = 1;
            $this->pdo->prepare("INSERT INTO id_sequel (tahun, nilai_terakhir) VALUES (?, ?)")->execute([$year, $running]);
        }
        return "AR-" . $year . "-" . str_pad($running, 5, '0', STR_PAD_LEFT);
    }

    public function hantarPermohonan($id_permohonan) {

        $validation = $this->validatePermohonan($id_permohonan);
        if ($validation !== true) {
            return $validation;
        }

        $id = (int) $id_permohonan;

        // Ensure all three mandatory documents exist before submission
        $check = $this->pdo->prepare("SELECT jenis_dokumen FROM dokumen WHERE id_permohonan = ?");
        $check->execute([$id]);
        $existingTypes = $check->fetchAll(PDO::FETCH_COLUMN);

        $requiredTypes = ['IC Pelajar', 'Gambar Pelajar', 'Sijil Pelajar'];
        $missing = array_diff($requiredTypes, $existingTypes);
        if (!empty($missing)) {
            return "Sila muat naik semua dokumen mandatori yang diperlukan sebelum menghantar: " . implode(', ', $missing) . ".";
        }

        $no_rujukan = $this->generateNoRujukan();
        $this->pdo->prepare("UPDATE permohonan SET no_rujukan = ?, kod_status = '03', langkah_semasa = 6, tarikh_hantar = NOW() WHERE id_permohonan = ?")
            ->execute([$no_rujukan, $id]);
        return $no_rujukan;
    }

    public function getUserApplications($id_pengguna) {
        $stmt = $this->pdo->prepare("
            SELECT p.id_permohonan, p.no_rujukan, p.kod_status, p.tarikh_cipta, p.tarikh_hantar, p.langkah_semasa,
                   pl.nama_penuh as nama_pelajar, pl.no_pelajar
            FROM permohonan p
            LEFT JOIN pelajar pl ON p.id_permohonan = pl.id_permohonan
            WHERE p.id_pengguna = ?
            ORDER BY p.tarikh_cipta DESC
        ");
        $stmt->execute([$id_pengguna]);
        return $stmt->fetchAll();
    }
}