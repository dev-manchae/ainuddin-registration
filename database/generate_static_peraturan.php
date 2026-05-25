<?php
require_once __DIR__ . '/../app/libs/fpdf.php';

class StaticPeraturanGenerator extends FPDF {
    
    // Header definition
    public function Header() {
        if ($this->PageNo() > 1) {
            // MTA Branding header
            $this->SetFont('Arial', 'B', 8.5);
            $this->SetTextColor(30, 86, 49); // MTA Teal
            $this->Cell(100, 5, "MAAHAD TAHFIZ 'AINUDDIN (MTA)", 0, 0, 'L');
            $this->SetFont('Arial', 'I', 8.5);
            $this->SetTextColor(100, 116, 139);
            $this->Cell(80, 5, "TATATERTIB & PERATURAN ASRAMA PELAJAR", 0, 1, 'R');
            
            // Thin rule
            $this->SetDrawColor(30, 86, 49);
            $this->SetLineWidth(0.2);
            $this->Line(15, 15, 195, 15);
            $this->Ln(4);
        }
    }

    // Footer definition
    public function Footer() {
        if ($this->PageNo() > 1) {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(148, 163, 184);
            $this->Cell(100, 10, "Sistem Pendaftaran Maahad Tahfiz 'Ainuddin (MTA)", 0, 0, 'L');
            $this->Cell(80, 10, "Halaman " . $this->PageNo() . " daripada {nb}", 0, 0, 'R');
        }
    }

    // Method to create the cover page
    public function generateCoverPage() {
        $this->AddPage();
        
        // Border
        $this->SetDrawColor(30, 86, 49);
        $this->SetLineWidth(1);
        $this->Rect(10, 10, 190, 277);
        $this->SetLineWidth(0.3);
        $this->Rect(12, 12, 186, 273);
        
        // Logo
        $logoPath = __DIR__ . '/../public/assets/images/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 85, 40, 40);
        }
        
        $this->Ln(80);
        
        // School Name
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(0, 10, "MAAHAD TAHFIZ 'AINUDDIN (MTA)", 0, 1, 'C');
        
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 8, "Lot 38221, Kampung Kurnia, Bukit Pekan, 31910 Kampar, Perak", 0, 1, 'C');
        $this->Cell(0, 6, "Hubungi: 019-236 4698", 0, 1, 'C');
        
        $this->Ln(25);
        
        // Document Title
        $this->SetDrawColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->SetTextColor(30, 86, 49);
        $this->SetFont('Arial', 'B', 14);
        
        $this->SetX(20);
        $this->Cell(170, 14, "DOKUMEN TATATERTIB & PERATURAN ASRAMA PELAJAR", 1, 1, 'C', true);
        
        $this->Ln(30);
        
        // Form Fields (Placeholder for student to fill physical copy or write)
        $this->SetTextColor(30, 41, 59);
        $this->SetFont('Arial', 'B', 10);
        
        $this->SetX(30);
        $this->Cell(40, 8, "Nama Pelajar : ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(110, 8, "....................................................................................................", 0, 1, 'L');
        
        $this->SetX(30);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, "No. Kad Pengenalan : ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(110, 8, "....................................................................................................", 0, 1, 'L');
        
        $this->SetX(30);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, "Tarikh Kemasukan : ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(110, 8, "....................................................................................................", 0, 1, 'L');
    }
    
    // Helper to print a section header
    public function printSectionHeader($title) {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(30, 86, 49);
        $this->SetFillColor(240, 248, 243);
        $this->Cell(0, 7, " " . $title, 0, 1, 'L', true);
        $this->Ln(2);
    }
    
    // Helper to print a subsection header
    public function printSubsectionHeader($title) {
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 9.5);
        $this->SetTextColor(30, 86, 49);
        $this->Cell(0, 6, $title, 0, 1, 'L');
        $this->Ln(1);
    }
    
    // Helper to print rule bullet items
    public function printRuleItem($num, $text) {
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(30, 41, 59);
        $this->Cell(10, 5, $num, 0, 0, 'L');
        $this->MultiCell(170, 5, $text, 0, 'J');
        $this->Ln(1);
    }
}

// Instantiate and compile the PDF
$pdf = new StaticPeraturanGenerator('P', 'mm', 'A4');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AliasNbPages();

// 1. Cover
$pdf->generateCoverPage();

// 2. Rules Content (Pages 6 to 15)
$pdf->AddPage();
$pdf->SetTextColor(30, 41, 59);

// Section 1: TATATERTIB
$pdf->printSectionHeader("BAHAGIAN A: TATATERTIB AM PELAJAR MTA");

$pdf->printSubsectionHeader("1. AKUJANJI PELAJAR");
$akujanji = [
    "1.1" => "Pelajar MTA mesti sentiasa berambut pendek, kemas dan mematuhi etika kekemasan diri yang ditetapkan oleh Maahad.",
    "1.2" => "Pelajar diwajibkan mendirikan solat fardhu secara berjemaah lima waktu di Surau Maahad dengan hadir sekurang-kurangnya 10 minit sebelum solat bermula.",
    "1.3" => "Pelajar tidak dibenarkan menghisap rokok, vape, atau menggunakan sebarang bahan larangan dan memabukkan seperti dadah, menghidu gam, dan seumpamanya.",
    "1.4" => "Pelajar dilarang membawa sebarang bentuk gajet (telefon bimbit, radio, kamera, media player, pen-drive) atau sebarang alat muzik tanpa kebenaran bertulis daripada pihak pengurusan.",
    "1.5" => "Pelajar dilarang menyimpan benda tajam (pisau, parang) atau bahan letupan/merbahaya (mercun, minyak tanah) di dalam asrama atau bilik darjah.",
    "1.6" => "Pelajar dilarang membuat bising, bergaduh, membuli, atau bergurau secara kasar dan keterlaluan.",
    "1.7" => "Pelajar dilarang meminjam, mengambil atau menggunakan harta benda orang lain atau harta awam Maahad tanpa kebenaran bertulis daripada pihak berkuasa Maahad.",
    "1.8" => "Pelajar dilarang melibatkan diri dalam aktiviti politik, membentuk pertubuhan luar, atau menyertai tunjuk perasaan.",
    "1.9" => "Pelajar dilarang menyebarkan berita buruk atau fitnah mengenai Maahad di media sosial, akhbar, internet, atau sebarang saluran lain.",
    "1.10" => "Pihak Maahad berhak memberhentikan (buang sekolah) mana-mana pelajar yang melakukan kesalahan disiplin berat seperti buli, pergaduhan, mencuri, dadah, atau melanggar peraturan berulang kali.",
    "1.11" => "Pihak pentadbiran Maahad, Mudir, Warden, dan Guru berhak melakukan pemeriksaan mengejut pada bila-bila masa siang atau malam ke atas barangan peribadi pelajar.",
    "1.12" => "Pihak pentadbiran MTA berhak meminda, menambah, atau memansuhkan sebarang peraturan dari semasa ke semasa demi kemaslahatan Maahad."
];
foreach ($akujanji as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("2. ADAB BERSAMA GURU DAN KAKITANGAN");
$adab = [
    "2.1" => "Setiap pelajar MTA mestilah menghormati semua ustaz, guru, pihak pentadbir, Warden, dan kakitangan MTA pada setiap masa.",
    "2.2" => "Setiap pelajar mestilah sedia berkhidmat kepada mereka dan bersedia menerima hukuman daripada ustaz/guru sekiranya melakukan kesalahan.",
    "2.3" => "Setiap pelajar mestilah bersalaman kepada mereka yang lebih tua (sesama jantina) dengan mencium tangan.",
    "2.4" => "Setiap pelajar mestilah melazimkan diri dengan akhlak yang baik dan berusaha meninggalkan akhlak yang buruk.",
    "2.5" => "Pelajar dilarang memasuki Pejabat Pentadbiran tanpa kebenaran."
];
foreach ($adab as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("3. PERATURAN SEMASA BELAJAR");
$belajar = [
    "3.1" => "Setiap pelajar MTA mestilah mematuhi jadual pembelajaran dan mengambil bahagian di setiap program yang telah ditetapkan seperti waktu belajar, waktu majlis agama, waktu makan, waktu riadah, waktu tidur dan lain-lain.",
    "3.2" => "Setiap pelajar mestilah mewujudkan suasana agama dan suasana belajar setiap masa.",
    "3.3" => "Setiap pelajar tidak dibenarkan keluar dari bilik darjah tanpa kebenaran ustaz/guru bertugas."
];
foreach ($belajar as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("4. PERATURAN ASRAMA (DORMITORI)");
$asrama = [
    "5.1" => "Setiap pelajar MTA mestilah menjaga kebersihan diri dan asrama.",
    "5.2" => "Setiap pelajar hendaklah menjaga dan mengemas tempat tidur dan almari masing-masing sebelum keluar.",
    "5.3" => "Setiap pelajar tidak dibenarkan berada di dalam bilik asrama ketika sesi pembelajaran berlangsung.",
    "5.4" => "Setiap pelajar tidak dibenarkan membawa orang luar (termasuk ibu bapa) masuk ke dalam asrama tanpa kebenaran Warden.",
    "5.5" => "Pelajar tidak dibenarkan menggunakan peralatan elektrik tambahan di asrama seperti cerek elektrik, heater, atau perkakas memasak sendiri.",
    "5.6" => "Pelajar asrama terikat dengan Peraturan Khas Komuniti Asrama MTA & arahan Warden Asrama dari masa ke semasa."
];
foreach ($asrama as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("5. PERATURAN AM");
$peraturan_am = [
    "6.1" => "Pelajar MTA mestilah mewujudkan suasana Agama dan belajar pada setiap masa serta menunjukkan akhlak yang baik di tengah masyarakat.",
    "6.2" => "Pelajar tidak dibenarkan membuat bising, bergaduh, atau menjalinkan hubungan dengan yang bukan mahram.",
    "6.3" => "Pelajar tidak dibenarkan mengkhianati rakan seperti menghasut orang, memukul, berbohong, menipu, tidak amanah, mencuri, mengambil barang orang lain, atau merosakkan harta benda.",
    "6.4" => "Pelajar dilarang berkelakuan lucah seperti mengintai, bercumbu, tidur berduaan, atau melukis bahan lucah.",
    "6.5" => "Pelajar tidak dibenarkan merosakkan harta sekolah termasuk juga mencuri, menconteng, membazir makanan, atau berlebihan menggunakan air dan elektrik.",
    "6.6" => "Pelajar tidak dibenarkan keluar atau berkeliaran dari kawasan yang Terlarang tanpa kebenaran bertulis.",
    "6.7" => "Pelajar MTA digalakkan berpuasa sunat setiap hari Isnin dan Khamis.",
    "6.8" => "Pelajar MTA mestilah mencukur rambut setiap 40 hari sekali, dan tidak dibenarkan mencukur Janggut.",
    "6.9" => "Pelajar MTA diminta mematuhi Pekeliling Pelajar MTA yang dikeluarkan dari masa ke semasa oleh pihak Pentadbir MTA.",
    "6.10" => "Pelajar dibenarkan membuat apa jua aduan kepada Mudir atau ustaz/guru atau pihak pentadbiran MTA.",
    "6.11" => "Pelajar dibenarkan berjumpa Keluarga atau waris pelajar setelah mendapat kebenaran daripada pihak MTA.",
    "6.12" => "Pelajar dibenarkan menerima dan membuat panggilan telefon kepada keluarga/penjaga pada waktu yang dibenarkan dengan kos panggilan ditanggung oleh pelajar.",
    "6.13" => "Setiap pelajar MTA hanya dibenarkan meninggalkan madrasah atau bercuti setelah mendapat Arahan Rasmi dari pihak Pentadbir MTA.",
    "6.14" => "Pihak MTA berhak 'buang sekolah' mana-mana pelajar yang melanggar Tatatertib Pelajar dan/atau gagal menunjukkan prestasi belajar dan/atau gagal hadir ke pengajian selama 3 hari berturut-turut tanpa sebab.",
    "6.15" => "Pihak MTA berhak meminda dan/atau menambah mana-mana peraturan yang difikirkan munasabah demi kebaikan bersama tanpa makluman bertulis kepada penjaga pelajar."
];
foreach ($peraturan_am as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

// Page 3: Peraturan Khas Komuniti Asrama
$pdf->AddPage();
$pdf->printSectionHeader("BAHAGIAN B: PERATURAN KHAS KOMUNITI ASRAMA MTA");

$pdf->printSubsectionHeader("1. DISIPLIN & TINGKAH LAKU KHAS");
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 5, "1.1 Tingkah Laku Jenayah (Dilarang Keras):", 0, 1, 'L');
$pdf->SetFont('Arial', '', 8.5);
$jenayahText = "Berjudi, Mencuri, Melawan/mengancam/memukul guru/warden/pengawas, Memeras ugut, Membuli, Menganggotai kumpulan haram/geng, Menyalahgunakan dadah, Membawa senjata merbahaya, Mencabul kehormatan, Menceroboh bilik khas/pejabat, Bertaruh, Berkelahi, Menghidu gam, Membawa minuman keras/memabukkan.";
$pdf->MultiCell(0, 4.5, $jenayahText, 0, 'J');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 5, "1.2 Tingkah Laku Kurang Sopan & Biadap:", 0, 1, 'L');
$pdf->SetFont('Arial', '', 8.5);
$biadapText = "Berkelakuan kasar/melawan Warden/Guru/pelajar lain, Berbahasa kesat, Menyimpan/menghisap rokok atau vape, Mengganggu pelajaran dan pembelajaran, Tidak menghormati guru, Ingkar arahan guru/pengawas, Mengejek orang lain.";
$pdf->MultiCell(0, 4.5, $biadapText, 0, 'J');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 5, "1.3 Tingkah Laku Lucah:", 0, 1, 'L');
$pdf->SetFont('Arial', '', 8.5);
$lucahText = "Bercumbu-cumbuan, Berkhalwat/berdua-duaan (bercinta), Menceroboh penempatan perempuan, Membawa bahan lucah, Mengintai, Berkelakuan/berkata lucah, Tidur berdua-duaan.";
$pdf->MultiCell(0, 4.5, $lucahText, 0, 'J');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 5, "1.4 Tiada Kekemasan Diri & Kekemasan Dorm:", 0, 1, 'L');
$pdf->SetFont('Arial', '', 8.5);
$kekemasanText = "Berambut panjang/berfesyen/berwarna, Berkuku panjang/kotor, Bermisai tanpa dirapi, Memakai pakaian tidak mengikut peraturan, Berpakaian comot/kotor/berkedut, Memakai perhiasan emas/perak, Mencukur bulu mata/kening, Tidak mengemas katil dan almari.";
$pdf->MultiCell(0, 4.5, $kekemasanText, 0, 'J');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 5, "1.5 Tindakan Tatatertib Bagi Kesalahan Disiplin:", 0, 1, 'L');
$pdf->SetFont('Arial', '', 8.5);
$tindakanText = "1. Tegur beramaran & khidmat denda (rekod)\n2. Tegur beramaran & wirid zikir / hafalan khas (rekod)\n3. Amaran (makluman bertulis kepada ibu bapa)\n4. Rotan 1 hingga 5 kali (makluman kepada ibu bapa)\n5. Buang asrama / penggantungan sementara\n6. Buang sekolah (ibu bapa dipanggil)\n7. Panggil polis bagi kes jenayah berat (mencuri, dadah, buli, senjata)\n8. Ganti rugi penuh bagi kes kerosakan harta benda.";
$pdf->MultiCell(0, 4.5, $tindakanText, 0, 'L');

$pdf->printSubsectionHeader("2. PERATURAN DI BILIK ASRAMA");
$bilik = [
    "2.1" => "Katil dan almari ditentukan oleh pihak MTA. Pelajar tidak dibenarkan membuat pertukaran tanpa kebenaran Warden.",
    "2.2" => "Setiap bilik asrama mesti melantik Ketua Bilik dan Penolong.",
    "2.3" => "Mestilah sentiasa kemas, bersih dan tersusun. Kain cadar biru muda hendaklah dipasang rapi, bantal bersarung biru muda, tuala dan pakaian disidai di tempat ampaian yang disediakan.",
    "2.4" => "Pelajar tidak dibenarkan memindah keluar / masuk apa-apa perabot / harta benda MTA tanpa kebenaran Warden.",
    "2.5" => "Pelajar digalakkan memasang kelambu semasa tidur."
];
foreach ($bilik as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("3. PERATURAN KELUAR MASUK ASRAMA");
$keluarMasuk = [
    "3.1" => "Balik Kampung: Pelajar hanya dibenarkan balik kampung mengikut ketetapan cuti persekolahan oleh MTA. Pelajar dikehendaki mendaftarkan diri menulis Buku Rekod Keluar dan mendapatkan Pas Keluar Asrama. Pelajar hendaklah datang semula selewat-lewatnya sebelum jam 6.00 petang.",
    "3.2" => "Keluar Pasar Malam: Pelajar dibenarkan keluar ke Pasar Malam pada petang hari Khamis mengikut giliran dengan memakai baju T asrama atau Jubah Putih serta berkopiah/serban. Keluar secara berkumpulan sekurang-kurangnya 4 orang dan pulang semula sebelum pukul 6.30 petang.",
    "3.3" => "Outing Hari Ahad: Pelajar dibenarkan keluar 'outing' sekiranya ditemani ibu bapa/penjaga sekali sahaja dalam tempoh sebulan. Pelajar dikehendaki menulis Buku Rekod Keluar dan memakai baju T asrama serta berkopiah.",
    "3.4" => "Kes Kecemasan: Bagi kes kecemasan (sakit teruk, kematian waris rapat), pelajar dibenarkan keluar asrama setelah memohon kebenaran daripada Warden dan mendaftar dalam Buku Rekod Keluar."
];
foreach ($keluarMasuk as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

// Page 4: Rules continued & clothing rules
$pdf->AddPage();
$pdf->printSubsectionHeader("4. PENGGUNAAN ASET & KEMUDAHAN ASRAMA");
$aset = [
    "4.1" => "Mesin Basuh: Penggunaan mesin basuh tertakluk kepada Jadual Giliran yang ditetapkan oleh Ketua Asrama.",
    "4.2" => "Telefon: Penggunaan telefon bagi setiap pelajar hanya dibenarkan 2 kali sebulan secara berbayar/mengikut kad talian.",
    "4.3" => "Seterika/Penggosok Baju: Pelajar dilarang menggunakan seterika peribadi. Kemudahan menggosok baju disediakan di bilik khas mengikut waktu terhad.",
    "4.4" => "Televisyen & Audio: Siaran televisyen dan penggunaan PA sistem adalah dengan kebenaran Warden sahaja.",
    "4.5" => "Elektrik & Air: Pelajar mestilah menjimatkan penggunaan air dan elektrik. Matikan suis lampu, kipas, dan pili air selepas digunakan.",
    "4.6" => "Kebersihan Kemudahan: Kebersihan asrama, bilik air, tandas, bilik basuhan dan kolah air adalah tanggungjawab bersama."
];
foreach ($aset as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("5. KUNJUNGAN PELAWAT");
$pelawat = [
    "5.1" => "Semua pelawat (termasuk ibu bapa/penjaga) tidak dibenarkan masuk ke bilik asrama tanpa kebenaran Warden bertugas.",
    "5.2" => "Penghuni asrama dilarang menjemput orang luar atau pelajar harian masuk ke kawasan asrama tanpa izin."
];
foreach ($pelawat as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("6. KESELAMATAN & KEBAKARAN");
$kebakaran = [
    "6.1" => "Dilarang keras membawa senjata tajam atau merbahaya ke kawasan asrama.",
    "6.2" => "Keselamatan harta benda pelajar adalah tanggungjawab masing-masing. Pelajar dikehendaki mengunci almari peribadi.",
    "6.3" => "Tindakan Kebakaran: Apabila mendengar loceng/siren amaran kecemasan berterusan, pelajar hendaklah segera keluar dari asrama ke tapak perhimpunan mengikut laluan kecemasan.",
    "6.4" => "Pencegahan Kebakaran: Pelajar dilarang menyalakan lilin, lingkaran ubat nyamuk, atau membuat sambungan elektrik secara haram."
];
foreach ($kebakaran as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->printSubsectionHeader("7. KEROSAKAN PERALATAN & KELENGKAPAN (KOS DENDA GANTI RUGI)");
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, "Pelajar yang merosakkan kemudahan asrama/sekolah dikehendaki membayar ganti rugi mengikut kadar berikut:", 0, 1, 'L');
$pdf->Ln(2);

// Damage table
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(15, 5, " Bil", 1, 0, 'C', true);
$pdf->Cell(110, 5, " Peralatan / Kemudahan", 1, 0, 'L', true);
$pdf->Cell(45, 5, " Harga Denda Ganti Rugi", 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8.5);
$brokenItems = [
    "Cermin Tingkap" => "RM 5.00",
    "Pili Air" => "RM 10.00",
    "Pelampung Kolah" => "RM 20.00",
    "Kotak Suis Lampu" => "RM 6.00",
    "Lampu Kalimantang" => "RM 15.00",
    "Kipas Angin / Suis" => "RM 45.00",
    "Tangki Flush Tandas" => "RM 50.00",
    "Pintu Tandas" => "RM 120.00",
    "Meja Belajar" => "RM 120.00",
    "Kerusi Plastik" => "RM 30.00",
    "Katil Dua Tingkat" => "RM 200.00",
    "Almari Pakaian Besi" => "RM 300.00",
    "Tilam Bujang" => "RM 150.00"
];
$idx = 1;
foreach ($brokenItems as $item => $price) {
    $pdf->Cell(15, 5, " " . $idx++, 1, 0, 'C');
    $pdf->Cell(110, 5, " " . $item, 1, 0, 'L');
    $pdf->Cell(45, 5, $price, 1, 1, 'C');
}

// Page 5: Dress code and Signature
$pdf->AddPage();
$pdf->printSectionHeader("BAHAGIAN C: PERATURAN PAKAIAN PELAJAR MTA");

$pdf->printSubsectionHeader("1. PERATURAN PAKAIAN WAKTU KELAS & ACARA RASMI");
$pakaianKelas = [
    "1.1" => "Waktu Kelas Rasmi (Ahad - Rabu): Jubah Putih bersih, Serban Putih dan Kopiah Putih serta memakai Name Tag.",
    "1.2" => "Hari Sabtu: Jubah atau Kurta berwarna Hitam, Serban Putih dan Kopiah Putih serta memakai Name Tag.",
    "1.3" => "Program Luar Maahad: Jubah Putih atau Kurta mengikut arahan semasa Ustaz/Guru bertugas.",
    "1.4" => "Waktu Riadah (Petang): Baju T-Shirt sukan MTA, seluar sukan panjang (track bottom hitam/biru gelap) dan berkasut sukan.",
    "1.5" => "Waktu Khidmat / Gotong-Royong: Baju T-Shirt biasa (tidak ketat/tiada logo tidak sopan) dan berkopiah.",
    "1.6" => "Waktu Makan di Dewan Makan: Pakaian mestilah bersih, berseluar panjang sopan, berbaju T-Shirt berkolar/kurta dan berkopiah.",
    "1.7" => "Keluar Asrama (Outing/Balik Kampung): Baju T-Shirt MTA atau jubah/kurta, berkopiah, berseluar panjang hitam/slack, dan berkasut hitam.",
    "1.8" => "Kumpulan Qasidah Maahad: Jubah khas qasidah ber-ridak, bersarung kopiah putih dan berkasut hitam."
];
foreach ($pakaianKelas as $num => $text) {
    $pdf->printRuleItem($num, $text);
}

$pdf->Ln(5);

// PENGAKUAN DAN AKUJANJI
$pdf->SetDrawColor(30, 86, 49);
$pdf->SetFillColor(240, 248, 243);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(30, 86, 49);
$pdf->Cell(0, 7, "PENGAKUAN & AKUJANJI IBU BAPA / PENJAGA DAN PELAJAR", 1, 1, 'C', true);

$pdf->Ln(2);
$pdf->SetFont('Arial', '', 9.5);
$pdf->SetTextColor(30, 41, 59);
$pengakuanText = "Bahawa kami, yang bertandatangan di bawah, telah membaca, memahami dan bersetuju untuk mematuhi segala Tatatertib Pelajar dan Peraturan Asrama Maahad Tahfiz 'Ainuddin (MTA) sepanjang tempoh pengajian anak/jagaan kami di institusi ini. Kami bersedia menerima sebarang tindakan disiplin termasuk penggantungan atau pembuangan daripada Maahad sekiranya berlaku sebarang perlanggaran peraturan tatatertib.";
$pdf->MultiCell(0, 5, $pengakuanText, 0, 'J');

$pdf->Ln(8);

// Signatures Columns
$yStartSign = $pdf->GetY();

// Column 1: Student
$pdf->SetXY(15, $yStartSign);
$pdf->Line(15, $yStartSign + 18, 65, $yStartSign + 18);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetXY(15, $yStartSign + 19);
$pdf->Cell(50, 4, "Tandatangan Pelajar", 0, 1, 'C');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(50, 4, "Nama: .......................................", 0, 1, 'L');
$pdf->Cell(50, 4, "No. KP: ...................................", 0, 1, 'L');

// Column 2: Parent/Guardian
$pdf->SetXY(75, $yStartSign);
$pdf->Line(75, $yStartSign + 18, 125, $yStartSign + 18);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetXY(75, $yStartSign + 19);
$pdf->Cell(50, 4, "Tandatangan Penjaga", 0, 1, 'C');
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetXY(75, $yStartSign + 23);
$pdf->Cell(50, 4, "Nama: .......................................", 0, 1, 'L');
$pdf->SetXY(75, $yStartSign + 27);
$pdf->Cell(50, 4, "No. KP: ...................................", 0, 1, 'L');

// Column 3: Mudir / Warden
$pdf->SetXY(135, $yStartSign);
$pdf->Line(135, $yStartSign + 18, 195, $yStartSign + 18);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetXY(135, $yStartSign + 19);
$pdf->Cell(60, 4, "Disahkan Oleh Warden / Mudir", 0, 1, 'C');
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetXY(135, $yStartSign + 23);
$pdf->Cell(60, 4, "Nama: .......................................", 0, 1, 'L');
$pdf->SetXY(135, $yStartSign + 27);
$pdf->Cell(60, 4, "Tarikh: ......................................", 0, 1, 'L');

// Save PDF
$pdf->Output('F', __DIR__ . '/../public/assets/docs/peraturan_mta.pdf');
echo "Branded peraturan_mta.pdf generated successfully!\n";
?>
