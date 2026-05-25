<?php

require_once "config/database.php";

class AdminController {

    private $pdo;

    public function __construct() {
        $this->pdo = getConnection();
    }

    // =========================
    // ADMIN LOGIN
    // =========================
    public function login($data) {
        $emel = strtolower(trim($data['emel']));
        $kata_laluan = $data['kata_laluan'];

        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM pengguna 
            WHERE emel = ? AND peranan = 'admin'
        ");
        $stmt->execute([$emel]);
        $admin = $stmt->fetch();

        if (!$admin) {
            return "Emel atau kata laluan salah.";
        }
        if (!password_verify($kata_laluan, $admin['kata_laluan_hash'])) {
            return "Emel atau kata laluan salah.";
        }

        $_SESSION['id_pengguna'] = $admin['id_pengguna'];
        $_SESSION['nama_penuh'] = $admin['nama_penuh'];
        $_SESSION['peranan'] = 'admin';
        return true;
    }

    // =========================
    // DASHBOARD STATS
    // =========================
    public function getStats() {
        $stats = [];

        $stmt = $this->pdo->query("SELECT COUNT(*) as jumlah FROM permohonan");
        $stats['jumlah_permohonan'] = $stmt->fetch()['jumlah'];

        $stmt = $this->pdo->query("
            SELECT ks.kod, ks.perihal, COUNT(p.id_permohonan) as jumlah
            FROM kod_status ks
            LEFT JOIN permohonan p ON ks.kod = p.kod_status
            GROUP BY ks.kod, ks.perihal
            ORDER BY ks.kod ASC
        ");
        $stats['ikut_status'] = $stmt->fetchAll();

        $stmt = $this->pdo->query("
            SELECT p.id_permohonan, p.no_rujukan, p.kod_status, p.tarikh_cipta,
                   pg.nama_penuh as nama_pemohon, pl.nama_penuh as nama_pelajar
            FROM permohonan p
            JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
            LEFT JOIN pelajar pl ON p.id_permohonan = pl.id_permohonan
            ORDER BY p.tarikh_cipta DESC
            LIMIT 10
        ");
        $stats['terkini'] = $stmt->fetchAll();

        $stmt = $this->pdo->query("
            SELECT kp.program, COUNT(pl.id_pelajar) as jumlah
            FROM kod_program kp
            LEFT JOIN pelajar pl ON kp.kod = pl.kod_program
            GROUP BY kp.kod, kp.program
            ORDER BY jumlah DESC
        ");
        $stats['ikut_program'] = $stmt->fetchAll();

        return $stats;
    }

    // =========================
    // SENARAI PERMOHONAN
    // =========================
    public function getApplications($filters = []) {
        $sql = "
            SELECT p.id_permohonan, p.no_rujukan, p.kod_status,
                   p.tarikh_cipta, p.tarikh_hantar,
                   pg.nama_penuh as nama_pemohon, pg.emel,
                   pl.nama_penuh as nama_pelajar, pl.no_kp, pl.no_pelajar,
                   kp.program
            FROM permohonan p
            JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
            LEFT JOIN pelajar pl ON p.id_permohonan = pl.id_permohonan
            LEFT JOIN kod_program kp ON pl.kod_program = kp.kod
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['kod_status'])) {
            $sql .= " AND p.kod_status = ?";
            $params[] = $filters['kod_status'];
        }
        if (!empty($filters['carian'])) {
            $sql .= " AND (
                pl.nama_penuh LIKE ? OR pl.no_kp LIKE ? OR
                p.no_rujukan LIKE ? OR pg.nama_penuh LIKE ? OR
                pg.emel LIKE ? OR pl.no_pelajar LIKE ?
            )";
            $like = '%' . $filters['carian'] . '%';
            array_push($params, $like, $like, $like, $like, $like, $like);
        }

        $sql .= " ORDER BY p.tarikh_cipta DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // =========================
    // GET STATUS LIST
    // =========================
    public function getStatusList() {
        $stmt = $this->pdo->query("SELECT * FROM kod_status ORDER BY kod ASC");
        return $stmt->fetchAll();
    }

    // =========================
    // VIEW APPLICATION (FULL DETAIL)
    // =========================
    public function getApplicationDetail($id_permohonan) {
        // Permohonan
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                pg.nama_penuh as nama_pemohon,
                pg.emel as emel_pemohon,
                pg.no_telefon as telefon_pemohon,
                ks.perihal as status_perihal
            FROM permohonan p
            JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
            JOIN kod_status ks ON p.kod_status = ks.kod
            WHERE p.id_permohonan = ?
        ");
        $stmt->execute([$id_permohonan]);
        $permohonan = $stmt->fetch();

        if (!$permohonan) {
            return null;
        }

        // Pelajar
        $stmt = $this->pdo->prepare("
            SELECT 
                pl.*,
                kn.negeri,
                kc.cawangan,
                kp.program
            FROM pelajar pl
            LEFT JOIN kod_negeri kn ON pl.kod_negeri = kn.kod
            LEFT JOIN kod_cawangan kc ON pl.kod_cawangan = kc.kod
            LEFT JOIN kod_program kp ON pl.kod_program = kp.kod
            WHERE pl.id_permohonan = ?
        ");
        $stmt->execute([$id_permohonan]);
        $pelajar = $stmt->fetch();

        // Keluarga
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM keluarga
            WHERE id_permohonan = ?
            ORDER BY id_keluarga ASC
        ");
        $stmt->execute([$id_permohonan]);
        $keluarga = $stmt->fetchAll();

        // Akademik
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM akademik
            WHERE id_permohonan = ?
        ");
        $stmt->execute([$id_permohonan]);
        $akademik = $stmt->fetch();

        // Kesihatan
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM kesihatan
            WHERE id_permohonan = ?
        ");
        $stmt->execute([$id_permohonan]);
        $kesihatan = $stmt->fetch();

        // Dokumen
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM dokumen
            WHERE id_permohonan = ?
        ");
        $stmt->execute([$id_permohonan]);
        $dokumenList = $stmt->fetchAll();
        $dokumen = [];
        foreach ($dokumenList as $row) {
            $dokumen[$row['jenis_dokumen']][] = $row;
        }

        // Log Status
        $stmt = $this->pdo->prepare("
            SELECT 
                ls.*,
                pg.nama_penuh as nama_admin,
                ks.perihal as status_perihal
            FROM log_status ls
            LEFT JOIN pengguna pg ON ls.dikemaskini_oleh = pg.id_pengguna
            LEFT JOIN kod_status ks ON ls.kod_status = ks.kod
            WHERE ls.id_permohonan = ?
            ORDER BY ls.tarikh DESC
        ");
        $stmt->execute([$id_permohonan]);
        $logStatus = $stmt->fetchAll();

        return [
            'permohonan' => $permohonan,
            'pelajar'     => $pelajar,
            'keluarga'    => $keluarga,
            'akademik'    => $akademik,
            'kesihatan'   => $kesihatan,
            'dokumen'     => $dokumen,
            'logStatus'   => $logStatus
        ];
    }

    // =========================
    // UPDATE STATUS
    // =========================
    public function updateStatus($id_permohonan, $kod_status, $catatan, $id_admin, $batch = null) {
        $allowed = ['04', '05'];
        if (!in_array($kod_status, $allowed)) {
            return "Kod status tidak sah.";
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT kod_status FROM permohonan WHERE id_permohonan = ? FOR UPDATE");
            $stmt->execute([$id_permohonan]);
            $current = $stmt->fetch();
            if (!$current) {
                $this->pdo->rollBack();
                return "Permohonan tidak dijumpai.";
            }
            if ($current['kod_status'] != '03') {
                $this->pdo->rollBack();
                return "Hanya permohonan berstatus 'Dihantar' boleh diproses.";
            }

            // If approved, we need to generate no_pelajar
            if ($kod_status == '04') {
                if ($batch === null || !preg_match('/^\d{2}$/', $batch)) {
                    $this->pdo->rollBack();
                    return "Batch tidak sah. Sila masukkan batch 2 digit.";
                }

                $this->generateNoPelajar($id_permohonan, $batch);

                // Verify no_pelajar was actually generated
                $check = $this->pdo->prepare("
                    SELECT no_pelajar FROM pelajar WHERE id_permohonan = ?
                ");
                $check->execute([$id_permohonan]);
                $generated = $check->fetchColumn();

                if (!$generated) {
                    $this->pdo->rollBack();
                    return "No Pelajar gagal dijana. Maklumat pelajar (cawangan/negeri) mungkin tidak lengkap.";
                }
            }

            // Update status
            $stmt = $this->pdo->prepare("
                UPDATE permohonan SET kod_status = ?, tarikh_kemaskini = NOW()
                WHERE id_permohonan = ?
            ");
            $stmt->execute([$kod_status, $id_permohonan]);

            // Log
            $stmt = $this->pdo->prepare("
                INSERT INTO log_status (id_permohonan, kod_status, catatan, dikemaskini_oleh)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id_permohonan, $kod_status, $catatan, $id_admin]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Ralat sistem: " . $e->getMessage();
        }
    }

    // =========================
    // GENERATE NO_PELAJAR (FORMAT: YYYYBB CN N NNNNN)
    // =========================
    private function generateNoPelajar($id_permohonan, $batch) {
        // Get pelajar data
        $stmt = $this->pdo->prepare("
            SELECT pl.id_pelajar, pl.no_pelajar, pl.kod_negeri, pl.kod_cawangan
            FROM pelajar pl
            WHERE pl.id_permohonan = ?
        ");
        $stmt->execute([$id_permohonan]);
        $pelajar = $stmt->fetch();

        if (!$pelajar || !empty($pelajar['no_pelajar'])) {
            return;
        }

        if (empty($pelajar['kod_cawangan']) || empty($pelajar['kod_negeri'])) {
            return;
        }

        $tahun = date('Y');
        $cawanganLetter = strtoupper(substr($pelajar['kod_cawangan'], 0, 1));
        $negeriCode = $pelajar['kod_negeri'];   // e.g., "08"

        // Yearly global running number
        $stmt = $this->pdo->prepare("SELECT nilai_terakhir FROM pelajar_sequel WHERE tahun = ?");
        $stmt->execute([$tahun]);
        $sequel = $stmt->fetch();

        if ($sequel) {
            $running = $sequel['nilai_terakhir'] + 1;
            $stmt = $this->pdo->prepare("UPDATE pelajar_sequel SET nilai_terakhir = ? WHERE tahun = ?");
            $stmt->execute([$running, $tahun]);
        } else {
            $running = 1;
            $stmt = $this->pdo->prepare("INSERT INTO pelajar_sequel (tahun, nilai_terakhir) VALUES (?, ?)");
            $stmt->execute([$tahun, $running]);
        }

        $no_pelajar = $tahun
                      . $batch
                      . $cawanganLetter
                      . $negeriCode
                      . str_pad($running, 5, '0', STR_PAD_LEFT);

        $stmt = $this->pdo->prepare("UPDATE pelajar SET no_pelajar = ? WHERE id_permohonan = ?");
        $stmt->execute([$no_pelajar, $id_permohonan]);
    }

    // =========================
    // GET PROGRAM STATS
    // =========================
    public function getProgramStats() {
        $stmt = $this->pdo->query("
            SELECT kp.program, COUNT(pl.id_pelajar) as jumlah
            FROM kod_program kp
            LEFT JOIN pelajar pl ON kp.kod = pl.kod_program
            GROUP BY kp.kod, kp.program
            ORDER BY jumlah DESC
        ");
        return $stmt->fetchAll();
    }
}