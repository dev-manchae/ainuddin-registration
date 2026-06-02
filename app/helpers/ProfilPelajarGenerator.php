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
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(false); // Force exact single page layout
    }

    // Page Header
    public function Header()
    {
        // Draw left logo
        $leftLogo = 'public/assets/images/logo.png';
        if (file_exists($leftLogo)) {
            $this->Image($leftLogo, 10, 6, 12, 12);
        }

        // Title Info
        $this->SetY(6);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(30, 86, 49); // #1e5631 Forest Green
        $this->SetX(24);
        $this->Cell(100, 5, "MAAHAD TAHFIZ 'AINUDDIN", 0, 1, 'L');
        
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(100, 116, 139); // Muted slate
        $this->SetX(24);
        $this->Cell(100, 4, "PROFIL PENDAFTARAN MASUK PELAJAR", 0, 1, 'L');

        // Double Horizontal divider line
        $this->SetDrawColor(30, 86, 49);
        $this->SetLineWidth(0.5);
        $this->Line(10, 20, 200, 20);
        $this->SetLineWidth(0.2);
        $this->Line(10, 21.0, 200, 21.0);

        // Position cursor for body content
        $this->SetY(23);
    }

    // Page Footer
    public function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 7.5);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 5, "Laporan profil ini dijana secara automatik oleh Sistem MTA. Halaman " . $this->PageNo() . "/{nb}", 0, 0, 'C');
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

        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(25, 6.5, "  No. Rujukan", 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(25, 6.5, "  " . $noRujukan, 1, 0, 'L');
        
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(22, 6.5, "  No. Pelajar", 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(25, 6.5, "  " . $noPelajar, 1, 0, 'L');

        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(22, 6.5, "  Tarikh Cetak", 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(25, 6.5, "  " . date('d/m/Y'), 1, 0, 'L');
        
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(22, 6.5, "  Status", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(24, 6.5, "  " . $statusPerihal, 1, 1, 'L');
        $this->SetTextColor(30, 41, 59);

        $this->Ln(5);

        // 1. MAKLUMAT PERIBADI PELAJAR
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(240, 248, 243);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(0, 6.5, " 1. MAKLUMAT PERIBADI PELAJAR", 1, 1, 'L', true);

        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8.5);

        $this->SetFillColor(248, 250, 252);
        $this->Cell(40, 6.0, "  Nama Penuh", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(150, 6.0, "  " . ($pl['nama_penuh'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 8.5);

        $this->Cell(40, 6.0, "  No. KP / Sijil Lahir", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($pl['no_kp'] ?? '-'), 1, 0, 'L');
        $this->Cell(40, 6.0, "  Jantina", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($pl['jantina'] ?? '-'), 1, 1, 'L');

        $this->Cell(40, 6.0, "  Tarikh Lahir", 1, 0, 'L', true);
        $tarikhLahir = !empty($pl['tarikh_lahir']) ? date('d F Y', strtotime($pl['tarikh_lahir'])) : '-';
        $this->Cell(55, 6.0, "  " . $tarikhLahir, 1, 0, 'L');
        $this->Cell(40, 6.0, "  Tempat Lahir", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($pl['tempat_lahir'] ?? '-'), 1, 1, 'L');

        $this->Cell(40, 6.0, "  Warganegara", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($pl['warganegara'] ?? 'Malaysia'), 1, 0, 'L');
        $this->Cell(40, 6.0, "  Cawangan MTA", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($pl['cawangan'] ?? '-'), 1, 1, 'L');

        $this->Cell(40, 6.0, "  Program Pengajian", 1, 0, 'L', true);
        $this->Cell(150, 6.0, "  " . ($pl['program'] ?? 'Hafazan Al-Quran & Akademik'), 1, 1, 'L');

        $alamat = ($pl['alamat'] ?? '-') . ", " . ($pl['negeri'] ?? '');
        $alamat = str_replace(["\r", "\n"], " ", $alamat);

        $this->Cell(40, 12.0, "  Alamat Kediaman", 1, 0, 'L', true);
        $yAlamat = $this->GetY();
        $this->SetXY(50, $yAlamat);
        $this->MultiCell(150, 6.0, " " . $alamat, 0, 'L');
        $this->Rect(50, $yAlamat, 150, 12.0);

        $this->SetXY(10, $yAlamat + 12.0);
        $this->Ln(5);

        // 2. MAKLUMAT KELUARGA / PENJAGA
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 6.5, " 2. MAKLUMAT IBU BAPA / PENJAGA", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);

        $penjagaList = array_values($kl ?: []);
        $p1 = $penjagaList[0] ?? null;
        $p2 = $penjagaList[1] ?? null;

        if ($p2) {
            $alamat1 = str_replace(["\r", "\n"], " ", $p1['alamat'] ?? '-');
            $alamat2 = str_replace(["\r", "\n"], " ", $p2['alamat'] ?? '-');

            $this->SetFont('Arial', '', 8.5);
            $this->SetFillColor(248, 250, 252);

            // Row 1: Hubungan
            $this->SetX(10);
            $this->Cell(22, 6.0, "  Hubungan", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell(68, 6.0, "  " . ($p1['jenis_penjaga'] ?? 'Penjaga'), 1, 0, 'L');
            $this->SetFont('Arial', '', 8.5);
            $this->Cell(10, 6.0, "", 0, 0);
            $this->Cell(22, 6.0, "  Hubungan", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell(68, 6.0, "  " . ($p2['jenis_penjaga'] ?? 'Penjaga'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8.5);

            // Row 2: Nama Penuh
            $this->SetX(10);
            $this->Cell(22, 6.0, "  Nama Penuh", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell(68, 6.0, "  " . ($p1['nama_penuh'] ?? '-'), 1, 0, 'L');
            $this->SetFont('Arial', '', 8.5);
            $this->Cell(10, 6.0, "", 0, 0);
            $this->Cell(22, 6.0, "  Nama Penuh", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell(68, 6.0, "  " . ($p2['nama_penuh'] ?? '-'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8.5);

            // Row 3: No Tel
            $this->SetX(10);
            $this->Cell(22, 6.0, "  No. Telefon", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . ($p1['no_telefon'] ?? '-'), 1, 0, 'L');
            $this->Cell(10, 6.0, "", 0, 0);
            $this->Cell(22, 6.0, "  No. Telefon", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . ($p2['no_telefon'] ?? '-'), 1, 1, 'L');

            // Row 4: Pekerjaan
            $this->SetX(10);
            $this->Cell(22, 6.0, "  Pekerjaan", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . ($p1['pekerjaan'] ?? '-'), 1, 0, 'L');
            $this->Cell(10, 6.0, "", 0, 0);
            $this->Cell(22, 6.0, "  Pekerjaan", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . ($p2['pekerjaan'] ?? '-'), 1, 1, 'L');

            // Row 5: Pendapatan
            $this->SetX(10);
            $this->Cell(22, 6.0, "  Pendapatan", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . (!empty($p1['pendapatan']) ? 'RM ' . number_format($p1['pendapatan'], 2) : '-'), 1, 0, 'L');
            $this->Cell(10, 6.0, "", 0, 0);
            $this->Cell(22, 6.0, "  Pendapatan", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . (!empty($p2['pendapatan']) ? 'RM ' . number_format($p2['pendapatan'], 2) : '-'), 1, 1, 'L');

            // Row 6: Emel
            $this->SetX(10);
            $this->Cell(22, 6.0, "  Emel", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . ($p1['emel'] ?? '-'), 1, 0, 'L');
            $this->Cell(10, 6.0, "", 0, 0);
            $this->Cell(22, 6.0, "  Emel", 1, 0, 'L', true);
            $this->Cell(68, 6.0, "  " . ($p2['emel'] ?? '-'), 1, 1, 'L');

            // Row 7: Alamat
            $this->SetX(10);
            $this->Cell(22, 12.0, "  Alamat", 1, 0, 'L', true);
            $yParentAlamat = $this->GetY();
            $this->SetXY(32, $yParentAlamat);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell(68, 6.0, " " . $alamat1, 0, 'L');
            $this->Rect(32, $yParentAlamat, 68, 12.0);

            $this->SetXY(110, $yParentAlamat);
            $this->SetFont('Arial', '', 8.5);
            $this->Cell(22, 12.0, "  Alamat", 1, 0, 'L', true);
            $this->SetXY(132, $yParentAlamat);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell(68, 6.0, " " . $alamat2, 0, 'L');
            $this->Rect(132, $yParentAlamat, 68, 12.0);

            $this->SetXY(10, $yParentAlamat + 12.0);
        } else {
            $p1 = $p1 ?: [];
            $alamat1 = str_replace(["\r", "\n"], " ", $p1['alamat'] ?? '-');

            $this->SetFont('Arial', '', 8.5);
            $this->SetFillColor(248, 250, 252);

            $this->SetX(10);
            $this->Cell(40, 6.0, "  Hubungan", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell(150, 6.0, "  " . ($p1['jenis_penjaga'] ?? 'Penjaga'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8.5);

            $this->SetX(10);
            $this->Cell(40, 6.0, "  Nama Penuh", 1, 0, 'L', true);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell(150, 6.0, "  " . ($p1['nama_penuh'] ?? '-'), 1, 1, 'L');
            $this->SetFont('Arial', '', 8.5);

            $this->SetX(10);
            $this->Cell(40, 6.0, "  No. Telefon", 1, 0, 'L', true);
            $this->Cell(150, 6.0, "  " . ($p1['no_telefon'] ?? '-'), 1, 1, 'L');

            $this->SetX(10);
            $this->Cell(40, 6.0, "  Pekerjaan", 1, 0, 'L', true);
            $this->Cell(150, 6.0, "  " . ($p1['pekerjaan'] ?? '-'), 1, 1, 'L');

            $this->SetX(10);
            $this->Cell(40, 6.0, "  Pendapatan", 1, 0, 'L', true);
            $this->Cell(150, 6.0, "  " . (!empty($p1['pendapatan']) ? 'RM ' . number_format($p1['pendapatan'], 2) : '-'), 1, 1, 'L');

            $this->SetX(10);
            $this->Cell(40, 6.0, "  Emel", 1, 0, 'L', true);
            $this->Cell(150, 6.0, "  " . ($p1['emel'] ?? '-'), 1, 1, 'L');

            $this->SetX(10);
            $this->Cell(40, 12.0, "  Alamat", 1, 0, 'L', true);
            $yParentAlamat = $this->GetY();
            $this->SetXY(50, $yParentAlamat);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell(150, 6.0, " " . $alamat1, 0, 'L');
            $this->Rect(50, $yParentAlamat, 150, 12.0);

            $this->SetXY(10, $yParentAlamat + 12.0);
        }

        $this->Ln(5);

        // 3. MAKLUMAT AKADEMIK & AL-QURAN
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 6.5, " 3. MAKLUMAT AKADEMIK & AL-QURAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8.5);

        // Top general akademik
        $this->SetFillColor(248, 250, 252);
        $this->Cell(40, 6.0, "  Sekolah Terdahulu", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(150, 6.0, "  " . ($ak['nama_sekolah'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 8.5);

        $this->Cell(40, 6.0, "  Tahap Penguasaan Quran", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($ak['tahap_quran'] ?? '-'), 1, 0, 'L');
        $this->Cell(40, 6.0, "  Status Khatam", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($ak['status_khatam'] ?? '-'), 1, 1, 'L');

        // Extract and format hafazan text
        $surahHafazanText = '-';
        if (!empty($ak['surah_hafazan'])) {
            $decoded = json_decode($ak['surah_hafazan'], true);
            $surahHafazanText = (is_array($decoded) && isset($decoded['surah_hafazan'])) ? $decoded['surah_hafazan'] : $ak['surah_hafazan'];
        }
        $surahHafazanText = str_replace(["\r", "\n"], " ", $surahHafazanText);

        $this->Cell(40, 12.0, "  Surah Hafazan (Jika Ada)", 1, 0, 'L', true);
        $yHafazan = $this->GetY();
        $this->SetXY(50, $yHafazan);
        $this->MultiCell(150, 6.0, " " . $surahHafazanText, 0, 'L');
        $this->Rect(50, $yHafazan, 150, 12.0);

        $this->SetXY(10, $yHafazan + 12.0);
        $this->Ln(4);

        // Subject Results tables side-by-side
        $akademikData = json_decode($ak['keputusan_akademik'] ?? '', true) ?: [];
        
        $agamaData = [];
        if (isset($ak['keputusan_agama']) && !empty($ak['keputusan_agama'])) {
            $agamaData = json_decode($ak['keputusan_agama'], true) ?: [];
        } elseif (isset($decoded) && isset($decoded['keputusan_agama'])) {
            $agamaData = $decoded['keputusan_agama'];
        }

        // Sub-headers for results
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(90, 6.0, "  Keputusan Akademik Sekolah Kebangsaan", 1, 0, 'L', true);
        $this->Cell(10, 6.0, "", 0, 0);
        $this->Cell(90, 6.0, "  Keputusan Sekolah Agama (SRA / KAFA / SMA)", 1, 1, 'L', true);

        // Column headers
        $this->SetX(10);
        $this->SetTextColor(30, 41, 59);
        $this->Cell(60, 5.5, "  Subjek", 1, 0, 'L');
        $this->Cell(30, 5.5, "  Gred", 1, 0, 'C');
        $this->Cell(10, 5.5, "", 0, 0);
        $this->Cell(60, 5.5, "  Subjek", 1, 0, 'L');
        $this->Cell(30, 5.5, "  Gred", 1, 1, 'C');

        $yRowStart = $this->GetY();
        $maxRows = max(count($akademikData), count($agamaData));
        $maxRows = max(1, $maxRows);

        $this->SetFont('Arial', '', 8);
        for ($i = 0; $i < $maxRows; $i++) {
            $yCurrentRow = $yRowStart + ($i * 5.0);

            // Academic cell
            $this->SetXY(10, $yCurrentRow);
            if (isset($akademikData[$i])) {
                $this->Cell(60, 5.0, "  " . ($akademikData[$i]['subjek'] ?? ''), 1, 0, 'L');
                $this->Cell(30, 5.0, "  " . ($akademikData[$i]['keputusan'] ?? ''), 1, 0, 'C');
            } else {
                if ($i == 0) {
                    $this->Cell(90, 5.0, "  Tiada keputusan akademik", 1, 0, 'C');
                } else {
                    $this->Cell(60, 5.0, "", 1, 0, 'L');
                    $this->Cell(30, 5.0, "", 1, 0, 'C');
                }
            }

            // Religious cell
            $this->SetXY(110, $yCurrentRow);
            if (isset($agamaData[$i])) {
                $this->Cell(60, 5.0, "  " . ($agamaData[$i]['subjek'] ?? ''), 1, 0, 'L');
                $this->Cell(30, 5.0, "  " . ($agamaData[$i]['keputusan'] ?? ''), 1, 0, 'C');
            } else {
                if ($i == 0) {
                    $this->Cell(90, 5.0, "  Tiada keputusan sekolah agama", 1, 0, 'C');
                } else {
                    $this->Cell(60, 5.0, "", 1, 0, 'L');
                    $this->Cell(30, 5.0, "", 1, 0, 'C');
                }
            }
        }

        $yNextSection = $yRowStart + ($maxRows * 5.0) + 5;
        $this->SetXY(10, $yNextSection);

        // 4. MAKLUMAT KESIHATAN & KECEMASAN (Left side) and 5. SENARAI DOKUMEN SOKONGAN (Right side)
        $ySideStart = $this->GetY();

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

        // Left Side: Kesihatan
        $this->SetXY(10, $ySideStart);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(90, 6.5, " 4. MAKLUMAT KESIHATAN & KECEMASAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8.5);
        $this->SetFillColor(248, 250, 252);
        
        $this->SetX(10);
        $this->Cell(35, 6.0, "  Rekod Alahan", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . $alahanText, 1, 1, 'L');

        $this->SetX(10);
        $this->Cell(35, 6.0, "  Penyakit Kronik", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . $penyakitText, 1, 1, 'L');

        $this->SetX(10);
        $this->Cell(35, 6.0, "  Pengambilan Ubat", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . $ubatText, 1, 1, 'L');

        $this->SetX(10);
        $this->Cell(35, 6.0, "  No. Tel Kecemasan", 1, 0, 'L', true);
        $this->Cell(55, 6.0, "  " . ($ks['nombor_kecemasan'] ?? '-'), 1, 1, 'L');

        $this->SetX(10);
        $this->Cell(35, 6.0, "  Kebenaran Rawatan", 1, 0, 'L', true);
        $kebenaran = ($ks['kebenaran_rawatan'] ?? '') === 'Ya' ? 'YA (Dibenarkan)' : 'TIDAK';
        $this->Cell(55, 6.0, "  " . $kebenaran, 1, 1, 'L');

        // Right Side: Dokumen Sokongan
        $this->SetXY(110, $ySideStart);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(90, 6.5, " 5. DOKUMEN SOKONGAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 8.5);
        $this->SetFillColor(248, 250, 252);

        $docKeys = [
            'IC Pelajar' => 'KP Pelajar / MyKid',
            'Gambar Pelajar' => 'Gambar Passport',
            'Sijil Pelajar' => 'Sijil Akademik / Hafazan'
        ];

        $yDocRow = $ySideStart + 6.5;
        foreach ($docKeys as $dbKey => $label) {
            $this->SetXY(110, $yDocRow);
            $hasDoc = isset($docsList[$dbKey]) && !empty($docsList[$dbKey]);
            $statusText = $hasDoc ? "[X] Dimuat Naik" : "[ ] Belum Dihantar";
            
            $this->Cell(22, 10.0, "  " . $dbKey, 1, 0, 'L', true);
            $this->Cell(43, 10.0, "  " . $label, 1, 0, 'L');
            
            if ($hasDoc) {
                $this->SetFont('Arial', 'B', 8.5);
                $this->SetTextColor(22, 101, 52); // Dark Green
            } else {
                $this->SetTextColor(153, 27, 27); // Dark Red
            }
            $this->Cell(25, 10.0, "  " . $statusText, 1, 1, 'L');
            $this->SetFont('Arial', '', 8.5);
            $this->SetTextColor(30, 41, 59);
            $yDocRow += 10.0;
        }

        // Verification Footer Box
        $this->SetXY(10, $ySideStart + 41.0);
        $this->SetDrawColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->SetFont('Arial', 'I', 8.5);
        $this->SetTextColor(30, 86, 49);
        $veriText = "Dokumen profil pelajar ini dijana secara automatik oleh Sistem Pendaftaran Tahfiz Ainuddin. Sebarang pindaan maklumat fizikal hendaklah ditandatangani dan disahkan oleh pihak pentadbiran MTA.";
        $this->MultiCell(190, 4.8, $veriText, 1, 'C', true);
    }
}
