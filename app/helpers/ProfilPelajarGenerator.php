<?php
// app/helpers/ProfilPelajarGenerator.php
require_once __DIR__ . '/../libs/fpdf.php';

class ProfilPelajarGenerator extends FPDF
{
    private $data;

    public function __construct($data)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->data = $data;
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(true, 15);
    }

    // Page Header
    public function Header()
    {
        // Draw left logo
        $leftLogo = 'public/assets/images/logo.png';
        if (file_exists($leftLogo)) {
            $this->Image($leftLogo, 15, 10, 15, 15);
        }

        // Title Info
        $this->SetY(10);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(30, 86, 49); // #1e5631 Forest Green
        $this->SetX(35);
        $this->Cell(100, 6, "MAAHAD TAHFIZ 'AINUDDIN", 0, 1, 'L');
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(100, 116, 139); // Muted slate
        $this->SetX(35);
        $this->Cell(100, 5, "PROFIL PENDAFTARAN MASUK PELAJAR", 0, 1, 'L');

        // Double Horizontal divider line
        $this->SetDrawColor(30, 86, 49);
        $this->SetLineWidth(0.6);
        $this->Line(15, 28, 195, 28);
        $this->SetLineWidth(0.2);
        $this->Line(15, 29.2, 195, 29.2);

        // Position cursor for body content
        $this->SetY(34);
    }

    // Page Footer
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 10, "Laporan profil ini dijana secara automatik oleh Sistem MTA. Halaman " . $this->PageNo() . "/{nb}", 0, 0, 'C');
    }

    // Generate Profile PDF
    public function generate()
    {
        $this->AliasNbPages();
        $this->AddPage();

        $this->SetTextColor(30, 41, 59); // Slate-800

        $p  = $this->data['permohonan'] ?? [];
        $pl = $this->data['pelajar'] ?? [];
        $kl = $this->data['keluarga'] ?? [];
        $ak = $this->data['akademik'] ?? [];
        $ks = $this->data['kesihatan'] ?? [];
        $docsList = $this->data['dokumen_list'] ?? [];

        // Meta Info Row
        $noRujukan = $p['no_rujukan'] ?? 'Draf';
        $noPelajar = $pl['no_pelajar'] ?? '-';
        $statusPerihal = $p['status_perihal'] ?? 'Draf';

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(45, 6, "  No. Rujukan", 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(45, 6, "  " . $noRujukan, 1, 0, 'L');
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(45, 6, "  No. Pelajar", 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(45, 6, "  " . $noPelajar, 1, 1, 'L');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(45, 6, "  Tarikh Cetak", 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(45, 6, "  " . date('d/m/Y H:i'), 1, 0, 'L');
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(45, 6, "  Status Permohonan", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(45, 6, "  " . $statusPerihal, 1, 1, 'L');
        $this->SetTextColor(30, 41, 59);

        $this->Ln(4);

        // 1. MAKLUMAT PERIBADI PELAJAR
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(240, 248, 243);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(0, 5.5, " 1. MAKLUMAT PERIBADI PELAJAR", 1, 1, 'L', true);

        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8);

        $this->SetFillColor(248, 250, 252);
        $this->Cell(45, 5.2, "  Nama Penuh", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(135, 5.2, "  " . ($pl['nama_penuh'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 8);

        $this->Cell(45, 5.2, "  No. KP / Sijil Lahir", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($pl['no_kp'] ?? '-'), 1, 0, 'L');
        $this->Cell(45, 5.2, "  Jantina", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($pl['jantina'] ?? '-'), 1, 1, 'L');

        $this->Cell(45, 5.2, "  Tarikh Lahir", 1, 0, 'L', true);
        $tarikhLahir = !empty($pl['tarikh_lahir']) ? date('d F Y', strtotime($pl['tarikh_lahir'])) : '-';
        $this->Cell(45, 5.2, "  " . $tarikhLahir, 1, 0, 'L');
        $this->Cell(45, 5.2, "  Tempat Lahir", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($pl['tempat_lahir'] ?? '-'), 1, 1, 'L');

        $this->Cell(45, 5.2, "  Warganegara", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($pl['warganegara'] ?? 'Malaysia'), 1, 0, 'L');
        $this->Cell(45, 5.2, "  Cawangan MTA", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($pl['cawangan'] ?? '-'), 1, 1, 'L');

        $this->Cell(45, 5.2, "  Program Pengajian", 1, 0, 'L', true);
        $this->Cell(135, 5.2, "  " . ($pl['program'] ?? 'Hafazan Al-Quran & Akademik'), 1, 1, 'L');

        $alamat = ($pl['alamat'] ?? '-') . ", " . ($pl['negeri'] ?? '');
        $alamat = str_replace(["\r", "\n"], " ", $alamat);

        $this->Cell(45, 10.4, "  Alamat Kediaman", 1, 0, 'L', true);
        $yAlamat = $this->GetY();
        $this->SetXY(60, $yAlamat);
        $this->MultiCell(135, 5.2, " " . $alamat, 0, 'L');
        $this->Rect(60, $yAlamat, 135, 10.4);

        $this->SetXY(15, $yAlamat + 10.4);
        $this->Ln(3);

        // 2. MAKLUMAT KELUARGA / PENJAGA
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 5.5, " 2. MAKLUMAT IBU BAPA / PENJAGA", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);

        $penjagaList = array_values($kl ?: []);
        $p1 = $penjagaList[0] ?? null;
        $p2 = $penjagaList[1] ?? null;

        if ($p2) {
            $alamat1 = str_replace(["\r", "\n"], " ", $p1['alamat'] ?? '-');
            $alamat2 = str_replace(["\r", "\n"], " ", $p2['alamat'] ?? '-');

            $this->SetFont('Arial', '', 8);
            $this->SetFillColor(248, 250, 252);

            // Row 1: Hubungan
            $this->SetX(15);
            $this->Cell(25, 5.2, "  Hubungan", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(60, 5.2, "  " . ($p1['jenis_penjaga'] ?? 'Penjaga'), 1, 0, 'L');
            $this->SetFont('Arial', '', 8);
            $this->Cell(10, 5.2, "", 0, 0);
            $this->Cell(25, 5.2, "  Hubungan", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(60, 5.2, "  " . ($p2['jenis_penjaga'] ?? 'Penjaga'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8);

            // Row 2: Nama Penuh
            $this->SetX(15);
            $this->Cell(25, 5.2, "  Nama Penuh", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(60, 5.2, "  " . ($p1['nama_penuh'] ?? '-'), 1, 0, 'L');
            $this->SetFont('Arial', '', 8);
            $this->Cell(10, 5.2, "", 0, 0);
            $this->Cell(25, 5.2, "  Nama Penuh", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(60, 5.2, "  " . ($p2['nama_penuh'] ?? '-'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8);

            // Row 3: No Tel
            $this->SetX(15);
            $this->Cell(25, 5.2, "  No. Telefon", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . ($p1['no_telefon'] ?? '-'), 1, 0, 'L');
            $this->Cell(10, 5.2, "", 0, 0);
            $this->Cell(25, 5.2, "  No. Telefon", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . ($p2['no_telefon'] ?? '-'), 1, 1, 'L');

            // Row 4: Pekerjaan
            $this->SetX(15);
            $this->Cell(25, 5.2, "  Pekerjaan", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . ($p1['pekerjaan'] ?? '-'), 1, 0, 'L');
            $this->Cell(10, 5.2, "", 0, 0);
            $this->Cell(25, 5.2, "  Pekerjaan", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . ($p2['pekerjaan'] ?? '-'), 1, 1, 'L');

            // Row 5: Pendapatan
            $this->SetX(15);
            $this->Cell(25, 5.2, "  Pendapatan", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . (!empty($p1['pendapatan']) ? 'RM ' . number_format($p1['pendapatan'], 2) : '-'), 1, 0, 'L');
            $this->Cell(10, 5.2, "", 0, 0);
            $this->Cell(25, 5.2, "  Pendapatan", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . (!empty($p2['pendapatan']) ? 'RM ' . number_format($p2['pendapatan'], 2) : '-'), 1, 1, 'L');

            // Row 6: Emel
            $this->SetX(15);
            $this->Cell(25, 5.2, "  Emel", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . ($p1['emel'] ?? '-'), 1, 0, 'L');
            $this->Cell(10, 5.2, "", 0, 0);
            $this->Cell(25, 5.2, "  Emel", 1, 0, 'L', true);
            $this->Cell(60, 5.2, "  " . ($p2['emel'] ?? '-'), 1, 1, 'L');

            // Row 7: Alamat
            $this->SetX(15);
            $this->Cell(25, 10.4, "  Alamat", 1, 0, 'L', true);
            $yParentAlamat = $this->GetY();
            $this->SetXY(40, $yParentAlamat);
            $this->SetFont('Arial', '', 7.5);
            $this->MultiCell(60, 5.2, " " . $alamat1, 0, 'L');
            $this->Rect(40, $yParentAlamat, 60, 10.4);

            $this->SetXY(110, $yParentAlamat);
            $this->SetFont('Arial', '', 8);
            $this->Cell(25, 10.4, "  Alamat", 1, 0, 'L', true);
            $this->SetXY(135, $yParentAlamat);
            $this->SetFont('Arial', '', 7.5);
            $this->MultiCell(60, 5.2, " " . $alamat2, 0, 'L');
            $this->Rect(135, $yParentAlamat, 60, 10.4);

            $this->SetXY(15, $yParentAlamat + 10.4);
        } else {
            $p1 = $p1 ?: [];
            $alamat1 = str_replace(["\r", "\n"], " ", $p1['alamat'] ?? '-');

            $this->SetFont('Arial', '', 8);
            $this->SetFillColor(248, 250, 252);

            $this->SetX(15);
            $this->Cell(45, 5.2, "  Hubungan", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(135, 5.2, "  " . ($p1['jenis_penjaga'] ?? 'Penjaga'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8);

            $this->SetX(15);
            $this->Cell(45, 5.2, "  Nama Penuh", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(135, 5.2, "  " . ($p1['nama_penuh'] ?? '-'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8);

            $this->SetX(15);
            $this->Cell(45, 5.2, "  No. Telefon", 1, 0, 'L', true);
            $this->Cell(135, 5.2, "  " . ($p1['no_telefon'] ?? '-'), 1, 1, 'L');

            $this->SetX(15);
            $this->Cell(45, 5.2, "  Pekerjaan", 1, 0, 'L', true);
            $this->Cell(135, 5.2, "  " . ($p1['pekerjaan'] ?? '-'), 1, 1, 'L');

            $this->SetX(15);
            $this->Cell(45, 5.2, "  Pendapatan", 1, 0, 'L', true);
            $this->Cell(135, 5.2, "  " . (!empty($p1['pendapatan']) ? 'RM ' . number_format($p1['pendapatan'], 2) : '-'), 1, 1, 'L');

            $this->SetX(15);
            $this->Cell(45, 5.2, "  Emel", 1, 0, 'L', true);
            $this->Cell(135, 5.2, "  " . ($p1['emel'] ?? '-'), 1, 1, 'L');

            $this->SetX(15);
            $this->Cell(45, 10.4, "  Alamat", 1, 0, 'L', true);
            $yParentAlamat = $this->GetY();
            $this->SetXY(60, $yParentAlamat);
            $this->SetFont('Arial', '', 7.5);
            $this->MultiCell(135, 5.2, " " . $alamat1, 0, 'L');
            $this->Rect(60, $yParentAlamat, 135, 10.4);

            $this->SetXY(15, $yParentAlamat + 10.4);
        }

        // =========================
        // PAGE 2
        // =========================
        $this->AddPage();

        // 3. MAKLUMAT AKADEMIK & AL-QURAN
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 5.5, " 3. MAKLUMAT AKADEMIK & AL-QURAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8);

        // Top general akademik
        $this->SetFillColor(248, 250, 252);
        $this->Cell(45, 5.2, "  Sekolah Terdahulu", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(135, 5.2, "  " . ($ak['nama_sekolah'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 8);

        $this->Cell(45, 5.2, "  Tahap Penguasaan Quran", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($ak['tahap_quran'] ?? '-'), 1, 0, 'L');
        $this->Cell(45, 5.2, "  Status Khatam", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($ak['status_khatam'] ?? '-'), 1, 1, 'L');

        // Extract and format hafazan text
        $surahHafazanText = '-';
        if (!empty($ak['surah_hafazan'])) {
            $decoded = json_decode($ak['surah_hafazan'], true);
            $surahHafazanText = (is_array($decoded) && isset($decoded['surah_hafazan'])) ? $decoded['surah_hafazan'] : $ak['surah_hafazan'];
        }
        $surahHafazanText = str_replace(["\r", "\n"], " ", $surahHafazanText);

        $this->Cell(45, 10.4, "  Surah Hafazan (Jika Ada)", 1, 0, 'L', true);
        $yHafazan = $this->GetY();
        $this->SetXY(60, $yHafazan);
        $this->MultiCell(135, 5.2, " " . $surahHafazanText, 0, 'L');
        $this->Rect(60, $yHafazan, 135, 10.4);

        $this->SetXY(15, $yHafazan + 10.4);
        $this->Ln(3);

        // Subject Results tables side-by-side
        $akademikData = json_decode($ak['keputusan_akademik'] ?? '', true) ?: [];
        
        $agamaData = [];
        if (isset($ak['keputusan_agama']) && !empty($ak['keputusan_agama'])) {
            $agamaData = json_decode($ak['keputusan_agama'], true) ?: [];
        } elseif (isset($decoded) && isset($decoded['keputusan_agama'])) {
            $agamaData = $decoded['keputusan_agama'];
        }

        // Sub-headers for results
        $this->SetX(15);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(85, 5.2, "  Keputusan Akademik Sekolah Kebangsaan", 1, 0, 'L', true);
        $this->Cell(10, 5.2, "", 0, 0);
        $this->Cell(85, 5.2, "  Keputusan Sekolah Agama (SRA / KAFA / SMA)", 1, 1, 'L', true);

        // Column headers
        $this->SetX(15);
        $this->SetTextColor(30, 41, 59);
        $this->Cell(55, 4.5, "  Subjek", 1, 0, 'L');
        $this->Cell(30, 4.5, "  Gred", 1, 0, 'C');
        $this->Cell(10, 4.5, "", 0, 0);
        $this->Cell(55, 4.5, "  Subjek", 1, 0, 'L');
        $this->Cell(30, 4.5, "  Gred", 1, 1, 'C');

        $yRowStart = $this->GetY();
        $maxRows = max(count($akademikData), count($agamaData));
        $maxRows = max(1, $maxRows);

        $this->SetFont('Arial', '', 7.5);
        for ($i = 0; $i < $maxRows; $i++) {
            $yCurrentRow = $yRowStart + ($i * 4.5);

            // Academic cell
            $this->SetXY(15, $yCurrentRow);
            if (isset($akademikData[$i])) {
                $this->Cell(55, 4.5, "  " . ($akademikData[$i]['subjek'] ?? ''), 1, 0, 'L');
                $this->Cell(30, 4.5, "  " . ($akademikData[$i]['keputusan'] ?? ''), 1, 0, 'C');
            } else {
                if ($i == 0) {
                    $this->Cell(85, 4.5, "  Tiada keputusan akademik", 1, 0, 'C');
                } else {
                    $this->Cell(55, 4.5, "", 1, 0, 'L');
                    $this->Cell(30, 4.5, "", 1, 0, 'C');
                }
            }

            // Religious cell
            $this->SetXY(110, $yCurrentRow);
            if (isset($agamaData[$i])) {
                $this->Cell(55, 4.5, "  " . ($agamaData[$i]['subjek'] ?? ''), 1, 0, 'L');
                $this->Cell(30, 4.5, "  " . ($agamaData[$i]['keputusan'] ?? ''), 1, 0, 'C');
            } else {
                if ($i == 0) {
                    $this->Cell(85, 4.5, "  Tiada keputusan sekolah agama", 1, 0, 'C');
                } else {
                    $this->Cell(55, 4.5, "", 1, 0, 'L');
                    $this->Cell(30, 4.5, "", 1, 0, 'C');
                }
            }
        }

        $yNextSection = $yRowStart + ($maxRows * 4.5) + 3;
        $this->SetXY(15, $yNextSection);

        // 4. MAKLUMAT KESIHATAN & KECEMASAN
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 5.5, " 4. MAKLUMAT KESIHATAN & KECEMASAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8);

        $alahanVal = isset($ks['alahan']) ? trim($ks['alahan']) : null;
        if ($alahanVal === null) {
            $alahanText = "Tiada Maklumat";
        } elseif ($alahanVal === "" || strcasecmp($alahanVal, "tiada") === 0) {
            $alahanText = "Tiada";
        } else {
            $alahanText = $alahanVal;
        }

        $penyakitVal = isset($ks['penyakit_kronik']) ? trim($ks['penyakit_kronik']) : null;
        if ($penyakitVal === null) {
            $penyakitText = "Tiada Maklumat";
        } elseif ($penyakitVal === "" || strcasecmp($penyakitVal, "tiada") === 0) {
            $penyakitText = "Tiada";
        } else {
            $penyakitText = $penyakitVal;
        }

        $ubatVal = isset($ks['pengambilan_ubat']) ? trim($ks['pengambilan_ubat']) : null;
        if ($ubatVal === null) {
            $ubatText = "Tiada Maklumat";
        } elseif ($ubatVal === "" || strcasecmp($ubatVal, "tiada") === 0) {
            $ubatText = "Tiada";
        } else {
            $ubatText = $ubatVal;
        }

        $this->SetFillColor(248, 250, 252);
        $this->Cell(45, 5.2, "  Rekod Alahan", 1, 0, 'L', true);
        $this->Cell(135, 5.2, "  " . $alahanText, 1, 1, 'L');

        $this->Cell(45, 5.2, "  Penyakit Kronik", 1, 0, 'L', true);
        $this->Cell(135, 5.2, "  " . $penyakitText, 1, 1, 'L');

        $this->Cell(45, 5.2, "  Pengambilan Ubat Semasa", 1, 0, 'L', true);
        $this->Cell(135, 5.2, "  " . $ubatText, 1, 1, 'L');

        $this->Cell(45, 5.2, "  No. Telefon Kecemasan", 1, 0, 'L', true);
        $this->Cell(45, 5.2, "  " . ($ks['nombor_kecemasan'] ?? '-'), 1, 0, 'L');
        $this->Cell(45, 5.2, "  Kebenaran Rawatan", 1, 0, 'L', true);
        $kebenaran = ($ks['kebenaran_rawatan'] ?? '') === 'Ya' ? 'YA (Dibenarkan)' : 'TIDAK';
        $this->Cell(45, 5.2, "  " . $kebenaran, 1, 1, 'L');

        $this->Ln(3);

        // 5. SENARAI DOKUMEN SOKONGAN
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 5.5, " 5. DOKUMEN SOKONGAN YANG DIKEMUKAKAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(248, 250, 252);

        $docKeys = [
            'IC Pelajar' => 'Salinan Kad Pengenalan Pelajar / MyKid',
            'Gambar Pelajar' => 'Gambar Berukuran Passport Pelajar',
            'Sijil Pelajar' => 'Salinan Sijil Akademik / Sijil Hafazan'
        ];

        foreach ($docKeys as $dbKey => $label) {
            $hasDoc = isset($docsList[$dbKey]) && !empty($docsList[$dbKey]);
            $statusText = $hasDoc ? "[X] Sudah Dimuat Naik" : "[ ] Belum Dihantar";
            
            $this->Cell(45, 5.2, "  " . $dbKey, 1, 0, 'L', true);
            $this->Cell(95, 5.2, "  " . $label, 1, 0, 'L');
            
            if ($hasDoc) {
                $this->SetFont('Arial', 'B', 8);
                $this->SetTextColor(22, 101, 52); // Dark Green
            } else {
                $this->SetTextColor(153, 27, 27); // Dark Red
            }
            $this->Cell(40, 5.2, "  " . $statusText, 1, 1, 'L');
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(30, 41, 59);
        }

        $this->Ln(4);

        // Verification Footer Box
        $this->SetDrawColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(30, 86, 49);
        $veriText = "Dokumen profil pelajar ini dijana secara automatik oleh Sistem Pendaftaran Tahfiz Ainuddin. Sebarang pindaan maklumat fizikal hendaklah ditandatangani dan disahkan oleh pihak pentadbiran MTA.";
        $this->MultiCell(0, 4.5, $veriText, 1, 'C', true);
    }
}
