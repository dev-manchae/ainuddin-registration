<?php
require_once __DIR__ . '/../libs/fpdf.php';

class SuratTawaranGenerator extends FPDF {
    private $data;

    public function __construct($data) {
        parent::__construct('P', 'mm', 'A4');
        $this->data = $data;
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(true, 15);
    }

    /**
     * Get Y position.
     * 
     * @return float
     */
    public function GetY() {
        return parent::GetY();
    }

    /**
     * Get X position.
     * 
     * @return float
     */
    public function GetX() {
        return parent::GetX();
    }

    // Page Header
    public function Header() {
        // Logo
        $logoPath = 'public/assets/images/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 10, 25);
        }
        
        // School Info
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(30, 86, 49); // MTA Teal color (#1e5631)
        $this->Cell(28); // Space for logo
        $this->Cell(0, 8, "MAAHAD TAHFIZ 'AINUDDIN (MTA)", 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(28);
        $this->Cell(0, 5, "Lot 38221, Kampung Kurnia, Bukit Pekan, 31910 Kampar, Perak", 0, 1, 'L');
        $this->Cell(28);
        $this->Cell(0, 5, "Tel: 019-236 4698 | Emel: info@ainuddin.edu.my", 0, 1, 'L');

        $this->Ln(4);
        
        // Double horizontal divider line
        $this->SetDrawColor(30, 86, 49);
        $this->SetLineWidth(0.8);
        $this->Line(15, 38, 195, 38);
        $this->SetLineWidth(0.2);
        $this->Line(15, 39.5, 195, 39.5);
        
        $this->Ln(6);
    }

    // Page Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 10, "Surat tawaran ini dijanakan secara automatik oleh Sistem Pendaftaran MTA. Halaman " . $this->PageNo() . "/{nb}", 0, 0, 'C');
    }

    // Dynamic offer letter page
    public function generateLetter() {
        $this->AliasNbPages();
        $this->AddPage();
        
        $this->SetTextColor(30, 41, 59); // Slate-800
        
        // 1. Reference and Date Block
        $this->SetFont('Arial', '', 10);
        $noRujukan = $this->data['permohonan']['no_rujukan'] ?? 'AR-2026-XXXX';
        
        // Format Date
        $tarikhHantar = $this->data['permohonan']['tarikh_hantar'] ?? date('Y-m-d H:i:s');
        $dateFormatted = date('d F Y', strtotime($tarikhHantar));
        
        $this->Cell(100, 5, "No. Rujukan : " . $noRujukan, 0, 0, 'L');
        $this->Cell(80, 5, "Tarikh : " . $dateFormatted, 0, 1, 'R');
        
        $this->Ln(4);

        // 2. Recipient Address
        $this->SetFont('Arial', 'B', 10);
        $namaPenjaga = $this->data['keluarga']['Bapa']['nama_penuh'] ?? $this->data['keluarga']['Penjaga']['nama_penuh'] ?? 'Ibu/Bapa/Penjaga';
        $alamatPenjaga = $this->data['keluarga']['Bapa']['alamat'] ?? $this->data['keluarga']['Penjaga']['alamat'] ?? 'Alamat Berdaftar';
        
        $this->Cell(0, 5, "Kepada:", 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $namaPenjaga, 0, 1, 'L');
        $this->MultiCell(100, 4, $alamatPenjaga, 0, 'L');
        
        $this->Ln(4);

        // 3. Subject Line
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, "TAWARAN KEMASUKAN MENGAJI DI MAAHAD TAHFIZ 'AINUDDIN (MTA) SESI 2026", 0, 1, 'L');
        $this->SetLineWidth(0.4);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(3);

        // 4. Letter Body
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, "Merujuk kepada perkara di atas, dengan sukacitanya dimaklumkan bahawa permohonan pendaftaran anak/jagaan tuan-puan telah LULUS dan DITERIMA untuk kemasukan sesi pengajian akademik dan hafazan Al-Quran di MTA.", 0, 'J');
        $this->Ln(3);

        // 5. Student Profile Box Table
        $this->SetFillColor(248, 250, 252); // Very light grey
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(180, 6, "MAKLUMAT PELAJAR & PENGAJIAN", 1, 1, 'L', true);
        
        $this->SetFont('Arial', '', 9);
        // Row 1
        $this->Cell(45, 6, "  Nama Pelajar", 1, 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(135, 6, "  " . ($this->data['pelajar']['nama_penuh'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 9);
        // Row 2
        $this->Cell(45, 6, "  No. Kad Pengenalan", 1, 0, 'L');
        $this->Cell(135, 6, "  " . ($this->data['pelajar']['no_kp'] ?? '-'), 1, 1, 'L');
        // Row 3
        $this->Cell(45, 6, "  Nama Sekolah Dahulu", 1, 0, 'L');
        $this->Cell(135, 6, "  " . ($this->data['akademik']['nama_sekolah'] ?? '-'), 1, 1, 'L');
        // Row 4
        $this->Cell(45, 6, "  Program Ditawarkan", 1, 0, 'L');
        $this->Cell(135, 6, "  Hafazan Al-Quran & Akademik", 1, 1, 'L');

        $this->Ln(4);

        // 6. Conditions & Probation details
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, "Tawaran kemasukan ini adalah tertakluk kepada syarat-syarat pengajian berikut:", 0, 'J');
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 5, " 1. ", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(170, 5, "Mengikuti program percubaan (Probation Period) selama 90 hari di Maahad.", 0, 1, 'L');
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 5, " 2. ", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(170, 5, "Mematuhi segala peraturan asrama dan tatatertib khas komuniti MTA yang ditetapkan.", 0, 1, 'L');

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 5, " 3. ", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(170, 5, "Menyerahkan dokumen-dokumen yang diperlukan semasa hari pendaftaran fizikal.", 0, 1, 'L');

        $this->Ln(4);

        // 7. Signature agreement text
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, "AKUJANJI & JAWAPAN PENERIMAAN TAWARAN", 0, 1, 'L');
        $this->SetFont('Arial', '', 9.5);
        $this->MultiCell(0, 4.5, "Bahawa saya bersetuju menerima tawaran ini dan bersedia mematuhi segala Tatatertib Pelajar MTA, Peraturan Asrama dan bersedia menerima tindakan disiplin sekiranya saya melanggar mana-mana peraturan sepanjang tempoh pengajian.", 0, 'J');

        $this->Ln(6);

        // 8. Signatures columns (3 columns)
        $yStartSign = $this->GetY();
        
        // Column 1: Student
        $this->SetXY(15, $yStartSign);
        $this->Line(15, $yStartSign + 18, 65, $yStartSign + 18);
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(15, $yStartSign + 19);
        $this->Cell(50, 4, "Tandatangan Pelajar", 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 4, "Nama: .......................................", 0, 1, 'L');
        $this->Cell(50, 4, "No. KP: ...................................", 0, 1, 'L');

        // Column 2: Parent/Guardian
        $this->SetXY(75, $yStartSign);
        $this->Line(75, $yStartSign + 18, 125, $yStartSign + 18);
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(75, $yStartSign + 19);
        $this->Cell(50, 4, "Tandatangan Penjaga", 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->SetXY(75, $yStartSign + 23);
        $this->Cell(50, 4, "Nama: .......................................", 0, 1, 'L');
        $this->SetXY(75, $yStartSign + 27);
        $this->Cell(50, 4, "No. KP: ...................................", 0, 1, 'L');

        // Column 3: Mudir / staff
        $this->SetXY(135, $yStartSign);
        $this->Line(135, $yStartSign + 18, 195, $yStartSign + 18);
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(135, $yStartSign + 19);
        $this->Cell(60, 4, "Disahkan Oleh MTA Staff", 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->SetXY(135, $yStartSign + 23);
        $this->Cell(60, 4, "Nama: .......................................", 0, 1, 'L');
        $this->SetXY(135, $yStartSign + 27);
        $this->Cell(60, 4, "Jawatan: ...................................", 0, 1, 'L');

        // 9. Go to Page 2: Checklist & Attachments
        $this->generateChecklistPage();

        // 10. Go to Page 3 & 4: Full Registration Details (Lampiran B)
        $this->generateRegistrationDetailsPage();
    }

    // Page 2: Checklist & dynamic document verify status
    private function generateChecklistPage() {
        $this->AddPage();
        
        $this->SetTextColor(30, 41, 59);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, "LAMPIRAN A: SENARAI DOKUMEN & KEPERLUAN HARI PENDAFTARAN", 0, 1, 'C');
        $this->Ln(4);

        $this->SetFont('Arial', '', 9.5);
        $this->MultiCell(0, 5, "Berikut adalah senarai dokumen sokongan dan keperluan asrama yang wajib dibawa bersama semasa melapor diri di Maahad. Sila semak status penyerahan dokumen online anda:", 0, 'J');
        $this->Ln(4);

        // Header Table
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(15, 8, " Bil", 1, 0, 'C', true);
        $this->Cell(125, 8, " Dokumen / Keperluan Pengajian", 1, 0, 'L', true);
        $this->Cell(40, 8, " Status Hantaran", 1, 1, 'C', true);

        // List of docs in database
        $uploadedDocs = $this->data['dokumen_list'] ?? [];
        
        $docKeys = [
            'IC Pelajar' => 'Salinan Kad Pengenalan Pelajar / MyKid',
            'Gambar Pelajar' => 'Gambar Berukuran Passport Pelajar',
            'Sijil Pelajar' => 'Salinan Sijil Akademik / Sijil Hafazan'
        ];

        $this->SetFont('Arial', '', 9);
        $index = 1;
        foreach ($docKeys as $dbKey => $label) {
            $hasDoc = isset($uploadedDocs[$dbKey]) && !empty($uploadedDocs[$dbKey]);
            $statusText = $hasDoc ? "[  /  ] Telah Dimuatnaik" : "[     ] Perlu Dibawa";
            
            $this->Cell(15, 7, " " . $index++, 1, 0, 'C');
            $this->Cell(125, 7, " " . $label, 1, 0, 'L');
            
            if ($hasDoc) {
                $this->SetTextColor(22, 101, 52); // Green for uploaded
                $this->SetFont('Arial', 'B', 9);
            } else {
                $this->SetTextColor(185, 28, 28); // Red for missing
            }
            $this->Cell(40, 7, $statusText, 1, 1, 'C');
            
            // Reset text colors
            $this->SetTextColor(30, 41, 59);
            $this->SetFont('Arial', '', 9);
        }

        // Additional items checklist (Manual check for parents)
        $manualItems = [
            "Salinan Kad Pengenalan Ibu Bapa / Penjaga",
            "Salinan Rekod Kesihatan Pelajar (Jika Ada Penyakit)",
            "Jubah Putih (2 helai) & Kopiah Putih (2 biji)",
            "Pakaian Harian Asrama (Kain pelikat, seluar slack hitam)",
            "Keperluan Tidur (Bantal, selimut, cadar warna biru muda)",
            "Alatulis & Buku Nota Catatan Tebal"
        ];

        foreach ($manualItems as $item) {
            $this->Cell(15, 7, " " . $index++, 1, 0, 'C');
            $this->Cell(125, 7, " " . $item, 1, 0, 'L');
            $this->Cell(40, 7, "[     ] Sediakan Fizikal", 1, 1, 'C');
        }

        $this->Ln(6);

        // General Notice block
        $this->SetDrawColor(241, 148, 148);
        $this->SetFillColor(254, 242, 242); // Red-50 Alert box
        $this->SetFont('Arial', 'B', 9.5);
        $this->SetTextColor(153, 27, 27); // Dark red
        $this->Cell(0, 6, "  PERINGATAN PENTING:", 'TRL', 1, 'L', true);
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(185, 28, 28);
        $noticeText = "Pihak pengurusan Maahad Tahfiz 'Ainuddin (MTA) berhak menangguhkan pendaftaran pelajar sekiranya terdapat sebarang maklumat palsu, dokumen sokongan penting yang hilang, atau perakuan yang tidak ditandatangani dengan lengkap.";
        $this->MultiCell(0, 4.5, "  " . $noticeText, 'BRL', 'J', true);
    }

    // Page 3 & 4: Dynamic Full Registration Details (Lampiran B)
    private function generateRegistrationDetailsPage() {
        // PAGE 3: Student and Family details
        $this->AddPage();
        $this->SetTextColor(30, 41, 59);

        // Header Title
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, "LAMPIRAN B: REKOD BUTIRAN PENDAFTARAN ONLINE PELAJAR", 0, 1, 'C');
        $this->Ln(3);

        // 1. MAKLUMAT PERIBADI PELAJAR
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(240, 248, 243); // MTA Light Green/Teal
        $this->SetTextColor(30, 86, 49);
        $this->Cell(0, 6, " 1. MAKLUMAT PERIBADI PELAJAR", 1, 1, 'L', true);
        
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 9);
        $p = $this->data['pelajar'] ?? [];
        
        // Row 1: Nama Penuh
        $this->SetFillColor(248, 250, 252);
        $this->Cell(45, 6, "  Nama Penuh", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(135, 6, "  " . ($p['nama_penuh'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 9);

        // Row 2: No. KP / Jantina
        $this->Cell(45, 6, "  No. KP / Sijil Lahir", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($p['no_kp'] ?? '-'), 1, 0, 'L');
        $this->Cell(45, 6, "  Jantina", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($p['jantina'] ?? '-'), 1, 1, 'L');

        // Row 3: Tarikh Lahir / Tempat Lahir
        $this->Cell(45, 6, "  Tarikh Lahir", 1, 0, 'L', true);
        $tarikhLahir = !empty($p['tarikh_lahir']) ? date('d F Y', strtotime($p['tarikh_lahir'])) : '-';
        $this->Cell(45, 6, "  " . $tarikhLahir, 1, 0, 'L');
        $this->Cell(45, 6, "  Tempat Lahir", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($p['tempat_lahir'] ?? '-'), 1, 1, 'L');

        // Row 4: Warganegara / Cawangan
        $this->Cell(45, 6, "  Warganegara", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($p['warganegara'] ?? 'Malaysia'), 1, 0, 'L');
        $this->Cell(45, 6, "  Cawangan MTA", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($p['cawangan'] ?? '-'), 1, 1, 'L');

        // Row 5: Program
        $this->Cell(45, 6, "  Program Pengajian", 1, 0, 'L', true);
        $this->Cell(135, 6, "  " . ($p['program'] ?? 'Hafazan Al-Quran & Akademik'), 1, 1, 'L');

        // Row 6: Alamat Penuh
        $alamat = ($p['alamat'] ?? '-') . ", " . ($p['negeri'] ?? '');
        $alamat = str_replace(["\r", "\n"], " ", $alamat);
        
        $this->Cell(45, 12, "  Alamat Kediaman", 1, 0, 'L', true);
        $this->MultiCell(135, 6, " " . $alamat, 1, 'L');

        $this->Ln(4);

        // 2. MAKLUMAT KELUARGA / PENJAGA
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 6, " 2. MAKLUMAT IBU BAPA / PENJAGA", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);

        $f = $this->data['keluarga']['Bapa'] ?? $this->data['keluarga']['Penjaga'] ?? [];
        $m = $this->data['keluarga']['Ibu'] ?? [];

        $yStartKeluarga = $this->GetY() + 2;
        
        // Titles
        $this->SetXY(15, $yStartKeluarga);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(85, 6, "Maklumat Bapa / Penjaga Utama", 'B', 0, 'L');
        
        $this->SetXY(110, $yStartKeluarga);
        $this->Cell(85, 6, "Maklumat Ibu", 'B', 0, 'L');
        
        $this->SetTextColor(30, 41, 59);
        $yRow = $yStartKeluarga + 7;
        
        // Row 1: Nama
        $this->SetXY(15, $yRow);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(20, 5, "Nama:", 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(65, 5, $f['nama_penuh'] ?? '-', 0, 0, 'L');
        
        $this->SetXY(110, $yRow);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(20, 5, "Nama:", 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(65, 5, $m['nama_penuh'] ?? '-', 0, 0, 'L');
        
        // Row 2: Telefon
        $yRow += 5.5;
        $this->SetXY(15, $yRow);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(20, 5, "No. Tel:", 0, 0, 'L');
        $this->Cell(65, 5, $f['no_telefon'] ?? '-', 0, 0, 'L');
        
        $this->SetXY(110, $yRow);
        $this->Cell(20, 5, "No. Tel:", 0, 0, 'L');
        $this->Cell(65, 5, $m['no_telefon'] ?? '-', 0, 0, 'L');

        // Row 3: Pekerjaan
        $yRow += 5.5;
        $this->SetXY(15, $yRow);
        $this->Cell(20, 5, "Pekerjaan:", 0, 0, 'L');
        $this->Cell(65, 5, $f['pekerjaan'] ?? '-', 0, 0, 'L');
        
        $this->SetXY(110, $yRow);
        $this->Cell(20, 5, "Pekerjaan:", 0, 0, 'L');
        $this->Cell(65, 5, $m['pekerjaan'] ?? '-', 0, 0, 'L');

        // Row 4: Pendapatan
        $yRow += 5.5;
        $this->SetXY(15, $yRow);
        $this->Cell(20, 5, "Pendapatan:", 0, 0, 'L');
        $this->Cell(65, 5, !empty($f['pendapatan']) ? 'RM ' . number_format($f['pendapatan'], 2) : '-', 0, 0, 'L');
        
        $this->SetXY(110, $yRow);
        $this->Cell(20, 5, "Pendapatan:", 0, 0, 'L');
        $this->Cell(65, 5, !empty($m['pendapatan']) ? 'RM ' . number_format($m['pendapatan'], 2) : '-', 0, 0, 'L');

        // Row 5: Emel
        $yRow += 5.5;
        $this->SetXY(15, $yRow);
        $this->Cell(20, 5, "Emel:", 0, 0, 'L');
        $this->Cell(65, 5, $f['emel'] ?? '-', 0, 0, 'L');
        
        $this->SetXY(110, $yRow);
        $this->Cell(20, 5, "Emel:", 0, 0, 'L');
        $this->Cell(65, 5, $m['emel'] ?? '-', 0, 0, 'L');

        // Row 6: Alamat
        $yRow += 5.5;
        $this->SetXY(15, $yRow);
        $this->Cell(20, 5, "Alamat:", 0, 0, 'L');
        $this->SetXY(35, $yRow);
        $alamatBapa = str_replace(["\r", "\n"], " ", $f['alamat'] ?? '-');
        $this->MultiCell(60, 4, $alamatBapa, 0, 'L');
        $yEndBapa = $this->GetY();
        
        $this->SetXY(110, $yRow);
        $this->Cell(20, 5, "Alamat:", 0, 0, 'L');
        $this->SetXY(130, $yRow);
        $alamatIbu = str_replace(["\r", "\n"], " ", $m['alamat'] ?? '-');
        $this->MultiCell(60, 4, $alamatIbu, 0, 'L');
        $yEndIbu = $this->GetY();
        
        $yNext = max($yEndBapa, $yEndIbu) + 5;
        
        // PAGE 4: Academic Results & Health
        $this->AddPage();
        $this->SetTextColor(30, 41, 59);

        // Header Title
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, "LAMPIRAN B (SAMBUNGAN): REKOD PENGAJIAN & KESIHATAN", 0, 1, 'C');
        $this->Ln(3);

        // 3. MAKLUMAT AKADEMIK & AL-QURAN
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 6, " 3. MAKLUMAT AKADEMIK & AL-QURAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 9);

        $a = $this->data['akademik'] ?? [];
        
        // Top general akademik
        $this->SetFillColor(248, 250, 252);
        $this->Cell(45, 6, "  Sekolah Terdahulu", 1, 0, 'L', true);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(135, 6, "  " . ($a['nama_sekolah'] ?? '-'), 1, 1, 'L');
        $this->SetFont('Arial', '', 9);

        $this->Cell(45, 6, "  Tahap Penguasaan Quran", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($a['tahap_quran'] ?? '-'), 1, 0, 'L');
        $this->Cell(45, 6, "  Status Khatam", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($a['status_khatam'] ?? '-'), 1, 1, 'L');

        // Extract and format hafazan text
        $surahHafazanText = '-';
        if (!empty($a['surah_hafazan'])) {
            $decoded = json_decode($a['surah_hafazan'], true);
            $surahHafazanText = (is_array($decoded) && isset($decoded['surah_hafazan'])) ? $decoded['surah_hafazan'] : $a['surah_hafazan'];
        }
        $surahHafazanText = str_replace(["\r", "\n"], " ", $surahHafazanText);
        
        $this->Cell(45, 12, "  Surah Hafazan (Jika Ada)", 1, 0, 'L', true);
        $this->MultiCell(135, 6, " " . $surahHafazanText, 1, 'L');

        $this->Ln(3);

        // Subject Results tables side-by-side
        $yTableStart = $this->GetY();
        $akademikData = json_decode($a['keputusan_akademik'] ?? '', true) ?: [];
        $agamaData = json_decode($a['keputusan_agama'] ?? '', true) ?: [];

        // Academic Table (Left Column)
        $this->SetXY(15, $yTableStart);
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(85, 5, "Keputusan Akademik Sekolah Kebangsaan", 0, 1, 'L');
        
        $this->SetX(15);
        $this->Cell(55, 5, " Subjek", 1, 0, 'L', true);
        $this->Cell(30, 5, " Gred", 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 8.5);
        $rowCount = 0;
        foreach ($akademikData as $row) {
            $this->SetX(15);
            $this->Cell(55, 5, " " . ($row['subjek'] ?? ''), 1, 0, 'L');
            $this->Cell(30, 5, ($row['keputusan'] ?? ''), 1, 1, 'C');
            $rowCount++;
        }
        if ($rowCount === 0) {
            $this->SetX(15);
            $this->Cell(85, 5, "Tiada keputusan akademik direkodkan", 1, 1, 'C');
        }
        $yTableEndAkademik = $this->GetY();

        // Religious Table (Right Column)
        $this->SetXY(110, $yTableStart);
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(85, 5, "Keputusan Sekolah Agama (SRA / KAFA / SMA)", 0, 1, 'L');
        
        $this->SetX(110);
        $this->Cell(55, 5, " Subjek", 1, 0, 'L', true);
        $this->Cell(30, 5, " Gred", 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 8.5);
        $rowCountAgama = 0;
        foreach ($agamaData as $row) {
            $this->SetX(110);
            $this->Cell(55, 5, " " . ($row['subjek'] ?? ''), 1, 0, 'L');
            $this->Cell(30, 5, ($row['keputusan'] ?? ''), 1, 1, 'C');
            $rowCountAgama++;
        }
        if ($rowCountAgama === 0) {
            $this->SetX(110);
            $this->Cell(85, 5, "Tiada keputusan sekolah agama direkodkan", 1, 1, 'C');
        }
        $yTableEndAgama = $this->GetY();

        $yNextSection = max($yTableEndAkademik, $yTableEndAgama) + 4;

        // 4. MAKLUMAT KESIHATAN & KECEMASAN
        $this->SetXY(15, $yNextSection);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 6, " 4. MAKLUMAT KESIHATAN & KECEMASAN", 1, 1, 'L', true);
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', '', 9);

        $h = $this->data['kesihatan'] ?? [];

        // Row 1: Alahan
        $this->SetFillColor(248, 250, 252);
        $this->Cell(45, 6, "  Rekod Alahan", 1, 0, 'L', true);
        $this->Cell(135, 6, "  " . ($h['alahan'] ?? 'Tiada / Tiada Maklumat'), 1, 1, 'L');

        // Row 2: Penyakit Kronik
        $this->Cell(45, 6, "  Penyakit Kronik", 1, 0, 'L', true);
        $this->Cell(135, 6, "  " . ($h['penyakit_kronik'] ?? 'Tiada / Tiada Maklumat'), 1, 1, 'L');

        // Row 3: Pengambilan Ubat
        $this->Cell(45, 6, "  Pengambilan Ubat Semasa", 1, 0, 'L', true);
        $this->Cell(135, 6, "  " . ($h['pengambilan_ubat'] ?? 'Tiada / Tiada Maklumat'), 1, 1, 'L');

        // Row 4: No Kecemasan / Kebenaran Rawatan
        $this->Cell(45, 6, "  No. Telefon Kecemasan", 1, 0, 'L', true);
        $this->Cell(45, 6, "  " . ($h['nombor_kecemasan'] ?? '-'), 1, 0, 'L');
        $this->Cell(45, 6, "  Kebenaran Rawatan Kecemasan", 1, 0, 'L', true);
        $kebenaran = ($h['kebenaran_rawatan'] ?? '') === 'Ya' ? 'YA (Dibenarkan)' : 'TIDAK / TIADA RAKAMAN';
        $this->Cell(45, 6, "  " . $kebenaran, 1, 1, 'L');

        $this->Ln(8);

        // Verification Footer Box
        $this->SetDrawColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->SetFont('Arial', 'I', 8.5);
        $this->SetTextColor(30, 86, 49);
        $veriText = "Dokumen Lampiran B ini dijana secara automatik oleh sistem pengurusan MTA berdasarkan maklumat yang dimasukkan secara atas talian oleh ibu bapa/penjaga. Sebarang pindaan maklumat fizikal hendaklah dilaporkan segera kepada pihak pentadbiran MTA.";
        $this->MultiCell(0, 4.5, $veriText, 1, 'C', true);
    }
}
