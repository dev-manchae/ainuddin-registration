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
        $this->Cell(0, 10, "Surat tawaran ini dijanakan secara komputer oleh Sistem Pendaftaran MTA. Halaman " . $this->PageNo() . "/{nb}", 0, 0, 'C');
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
}
