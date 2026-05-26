<?php

require_once "app/helpers/csrf.php";
require_once "app/controllers/AuthController.php";
require_once "app/controllers/PermohonanController.php";
require_once "app/controllers/AdminController.php";
require_once "app/middleware/AuthMiddleware.php";
require_once "app/middleware/AdminMiddleware.php";

session_start();

// Ensure CSRF token exists for all pages
generateCsrfToken();

 $page = $_GET['page'] ?? 'home';

switch ($page) {

    // =========================
    // HOME
    // =========================
    case 'home':

        require_once "views/home.php";

        break;

    // =========================
    // REGISTER
    // =========================
    case 'register':

        require_once "views/auth/register.php";

        break;

    case 'register_process':

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=register");
            exit;
        }

        $authController = new AuthController();

        $result = $authController->register($_POST);

        if ($result === true) {

            $_SESSION['success'] = "Pendaftaran berjaya.";

            header("Location: ?page=login");

        } else {

            $_SESSION['error'] = $result;

            header("Location: ?page=register");
        }

        exit;

    // =========================
    // LOGIN
    // =========================
    case 'login':

        require_once "views/auth/login.php";

        break;

    case 'login_process':

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=login");
            exit;
        }

        $authController = new AuthController();

        $result = $authController->login($_POST);

        if ($result === true) {

            session_regenerate_id(true);

            header("Location: ?page=dashboard");

        } else {

            $_SESSION['error'] = $result;

            header("Location: ?page=login");
        }

        exit;

    // =========================
    // FORGOT PASSWORD
    // =========================
    case 'lupa_kata_laluan':

        require_once "views/auth/forgot_password.php";

        break;

    case 'proses_lupa':

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=lupa_kata_laluan");
            exit;
        }

        $authController = new AuthController();

        $result = $authController->forgotPassword($_POST);

        if ($result === true) {
            $_SESSION['success'] = "Jika e-mel wujud dalam sistem, pautan set semula telah dihantar ke e-mel anda.";
        } else {
            $_SESSION['error'] = $result;
        }

        header("Location: ?page=lupa_kata_laluan");
        exit;

    case 'reset_kata_laluan':

        $token = $_GET['token'] ?? null;
        if (!$token) {
            $_SESSION['error'] = "Pautan tidak sah.";
            header("Location: ?page=login");
            exit;
        }

        require_once "views/auth/reset_password.php";

        break;

    case 'proses_reset':

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=login");
            exit;
        }

        $authController = new AuthController();

        $result = $authController->resetPassword($_POST);

        if ($result === true) {
            session_regenerate_id(true);
            $_SESSION['success'] = "Kata laluan berjaya dikemaskini. Sila log masuk dengan kata laluan baru anda.";
            header("Location: ?page=login");
        } else {
            $_SESSION['error'] = $result;
            header("Location: ?page=reset_kata_laluan&token=" . urlencode($_POST['token'] ?? ''));
        }
        exit;

    // =========================
    // DASHBOARD
    // =========================
    case 'dashboard':
    case 'my_applications':

        AuthMiddleware::check();

        $content = "views/dashboard.php";

        require_once "views/layouts/header.php";

        require_once $content;

        require_once "views/layouts/footer.php";

        break;

    // =========================
    // MULA PERMOHONAN
    // =========================
    case 'mula_permohonan':

        AuthMiddleware::check();

        $permohonanController = new PermohonanController();

        $id_permohonan = $permohonanController->createDraft(
            $_SESSION['id_pengguna']
        );

        if ($id_permohonan === 0) {
            $_SESSION['error'] = "Gagal mencipta draf permohonan. Sila cuba lagi.";
            header("Location: ?page=dashboard");
            exit;
        }

        $_SESSION['id_permohonan'] = $id_permohonan;

        header("Location: ?page=step1");

        exit;

    // =========================
    // STEP 1
    // =========================
    case 'step1':

        AuthMiddleware::check();
        validatePermohonanSession();

        $permohonanController = new PermohonanController();

        $negeriList = $permohonanController->getNegeri();

        $cawanganList = $permohonanController->getCawangan();

        $programList = $permohonanController->getProgram();

        $pelajar = $permohonanController->getPelajar(
            $_SESSION['id_permohonan']
        );

        $langkah_semasa = getLangkahSemasa($_SESSION['id_permohonan']);

        $content = "views/registration/step1.php";

        require_once "views/layouts/registration_layout.php";

        break;

    case 'save_step1':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step1");
            exit;
        }

        $permohonanController = new PermohonanController();

        $is_draft = isset($_POST['simpan_dan_keluar']);
        $result = $permohonanController->savePelajar(
            $_POST,
            $_SESSION['id_permohonan'],
            $is_draft
        );

        if ($result === true) {

            $_SESSION['success'] = $is_draft ? "Draf permohonan berjaya disimpan." : "Maklumat pelajar berjaya disimpan.";

            header("Location: " . ($is_draft ? "?page=dashboard" : "?page=step2"));

        } else {

            $_SESSION['error'] = $result;
            header("Location: ?page=step1");
        }

        exit;

    // =========================
    // STEP 2
    // =========================
    case 'step2':

        AuthMiddleware::check();
        validatePermohonanSession();

        $langkah_semasa = getLangkahSemasa($_SESSION['id_permohonan']);

        if ($langkah_semasa < 2) {
            header("Location: ?page=step1");
            exit;
        }

        $permohonanController = new PermohonanController();

        $keluarga = $permohonanController->getKeluarga(
            $_SESSION['id_permohonan']
        );

        $content = "views/registration/step2.php";

        require_once "views/layouts/registration_layout.php";

        break;

    case 'save_step2':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step2");
            exit;
        }

        $permohonanController = new PermohonanController();

        $is_draft = isset($_POST['simpan_dan_keluar']);
        $result = $permohonanController->saveKeluarga(
            $_POST,
            $_SESSION['id_permohonan'],
            $is_draft
        );

        if ($result === true) {

            $_SESSION['success'] = $is_draft ? "Draf permohonan berjaya disimpan." : "Maklumat keluarga berjaya disimpan.";

            header("Location: " . ($is_draft ? "?page=dashboard" : "?page=step3"));

        } else {

            $_SESSION['error'] = $result;
            header("Location: ?page=step2");
        }

        exit;

    // =========================
    // STEP 3
    // =========================
    case 'step3':

        AuthMiddleware::check();
        validatePermohonanSession();

        $langkah_semasa = getLangkahSemasa($_SESSION['id_permohonan']);

        if ($langkah_semasa < 3) {
            header("Location: ?page=step" . $langkah_semasa);
            exit;
        }

        $permohonanController = new PermohonanController();

        $akademik = $permohonanController->getAkademik(
            $_SESSION['id_permohonan']
        );

        $content = "views/registration/step3.php";

        require_once "views/layouts/registration_layout.php";

        break;

    case 'save_step3':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step3");
            exit;
        }

        $permohonanController = new PermohonanController();

        $is_draft = isset($_POST['simpan_dan_keluar']);
        $result = $permohonanController->saveAkademik(
            $_POST,
            $_SESSION['id_permohonan'],
            $is_draft
        );

        if ($result === true) {

            $_SESSION['success'] = $is_draft ? "Draf permohonan berjaya disimpan." : "Maklumat akademik berjaya disimpan.";

            header("Location: " . ($is_draft ? "?page=dashboard" : "?page=step4"));

        } else {

            $_SESSION['error'] = $result;
            header("Location: ?page=step3");
        }

        exit;

    // =========================
    // STEP 4
    // =========================
    case 'step4':

        AuthMiddleware::check();
        validatePermohonanSession();

        $langkah_semasa = getLangkahSemasa($_SESSION['id_permohonan']);

        if ($langkah_semasa < 4) {
            header("Location: ?page=step" . $langkah_semasa);
            exit;
        }

        $permohonanController = new PermohonanController();

        $kesihatan = $permohonanController->getKesihatan(
            $_SESSION['id_permohonan']
        );

        $content = "views/registration/step4.php";

        require_once "views/layouts/registration_layout.php";

        break;

    case 'save_step4':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step4");
            exit;
        }

        $permohonanController = new PermohonanController();

        $is_draft = isset($_POST['simpan_dan_keluar']);
        $result = $permohonanController->saveKesihatan(
            $_POST,
            $_SESSION['id_permohonan'],
            $is_draft
        );

        if ($result === true) {

            $_SESSION['success'] = $is_draft ? "Draf permohonan berjaya disimpan." : "Maklumat kesihatan berjaya disimpan.";

            header("Location: " . ($is_draft ? "?page=dashboard" : "?page=step5"));

        } else {

            $_SESSION['error'] = $result;
            header("Location: ?page=step4");
        }

        exit;

    // =========================
    // STEP 5
    // =========================
    case 'step5':

        AuthMiddleware::check();
        validatePermohonanSession();

        $langkah_semasa = getLangkahSemasa($_SESSION['id_permohonan']);

        if ($langkah_semasa < 5) {
            header("Location: ?page=step" . $langkah_semasa);
            exit;
        }

        $permohonanController = new PermohonanController();

        $dokumen = $permohonanController->getDokumen(
            $_SESSION['id_permohonan']
        );

        $content = "views/registration/step5.php";

        require_once "views/layouts/registration_layout.php";

        break;

    case 'save_step5':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step5");
            exit;
        }

        $permohonanController = new PermohonanController();

        $is_draft = isset($_POST['simpan_dan_keluar']);
        $result = $permohonanController->saveDokumen(
            $_FILES,
            $_SESSION['id_permohonan'],
            $is_draft
        );

        if ($result === true) {

            $_SESSION['success'] = $is_draft ? "Draf permohonan berjaya disimpan." : "Dokumen berjaya dimuat naik.";

            header("Location: " . ($is_draft ? "?page=dashboard" : "?page=step6"));

        } else {

            $_SESSION['error'] = $result;
            header("Location: ?page=step5");
        }

        exit;

    case 'delete_dokumen':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step5");
            exit;
        }

        $id_dokumen = $_POST['id_dokumen'] ?? null;
        if ($id_dokumen) {
            $permohonanController = new PermohonanController();
            $result = $permohonanController->deleteDokumenById($id_dokumen, $_SESSION['id_permohonan']);
            if ($result === true) {
                $_SESSION['success'] = "Dokumen berjaya dipadam.";
            } else {
                $_SESSION['error'] = $result;
            }
        }

        header("Location: ?page=step5");
        exit;

    // =========================
    // STEP 6
    // =========================
    case 'step6':

        AuthMiddleware::check();
        validatePermohonanSession();

        $langkah_semasa = getLangkahSemasa($_SESSION['id_permohonan']);

        if ($langkah_semasa < 6) {
            header("Location: ?page=step" . $langkah_semasa);
            exit;
        }

        $content = "views/registration/step6.php";

        require_once "views/layouts/registration_layout.php";

        break;

    // =========================
    // SUBMIT PERMOHONAN
    // =========================
    case 'submit_permohonan':

        AuthMiddleware::check();
        validatePermohonanSession();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=step6");
            exit;
        }

        $permohonanController = new PermohonanController();

        $result = $permohonanController->hantarPermohonan(
            $_SESSION['id_permohonan']
        );

        if (is_string($result) && strpos($result, 'AR-') === 0) {
            $no_rujukan = $result;
            unset($_SESSION['id_permohonan']);
            $langkah_semasa = 6;
            $content = "views/registration/success.php";
            require_once "views/layouts/registration_layout.php";
        } else {
            $_SESSION['error'] = $result;
            header("Location: ?page=step6");
            exit;
        }

        break;

    // =========================
    // LOGOUT
    // =========================
    case 'logout':

        session_destroy();

        header("Location: ?page=login");

        exit;

    // =========================
    // RESUME DRAFT
    // =========================
    case 'resume_permohonan':
        AuthMiddleware::check();

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: ?page=dashboard");
            exit;
        }

        $id = (int)$_GET['id'];

        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT id_permohonan, langkah_semasa
            FROM permohonan
            WHERE id_permohonan = ? AND id_pengguna = ? AND kod_status IN ('00', '08')
        ");
        $stmt->execute([$id, $_SESSION['id_pengguna']]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['error'] = "Permohonan tidak dijumpai atau tidak lagi boleh disambung.";
            header("Location: ?page=dashboard");
            exit;
        }

        $_SESSION['id_permohonan'] = $row['id_permohonan'];
        header("Location: ?page=step" . ($row['langkah_semasa'] ?: 1));
        exit;

    // =========================
    // DELETE PERMOHONAN (DRAFT ONLY)
    // =========================
    case 'delete_permohonan':
        AuthMiddleware::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_permohonan'])) {
            header("Location: ?page=dashboard");
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=dashboard");
            exit;
        }

        $permohonanController = new PermohonanController();
        
        $result = $permohonanController->deleteDraft(
            $_POST['id_permohonan'], 
            $_SESSION['id_pengguna']
        );

        if ($result === true) {
            if (isset($_SESSION['id_permohonan']) && $_SESSION['id_permohonan'] == $_POST['id_permohonan']) {
                unset($_SESSION['id_permohonan']);
            }
            $_SESSION['success'] = "Draf permohonan berjaya dipadam.";
        } else {
            $_SESSION['error'] = $result;
        }

        header("Location: ?page=dashboard");
        exit;

    // =========================
    // ADMIN ROUTES
    // =========================

    case 'admin_login':

        require_once "views/admin/login.php";

        break;

    case 'admin_login_process':

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=admin_login");
            exit;
        }

        $adminController = new AdminController();

        $result = $adminController->login($_POST);

        if ($result === true) {

            session_regenerate_id(true);

            header("Location: ?page=admin_dashboard");

        } else {

            $_SESSION['error'] = $result;

            header("Location: ?page=admin_login");
        }

        exit;

    case 'cetak_surat_tawaran':

        AuthMiddleware::check();

        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT id_permohonan, kod_status FROM permohonan WHERE id_pengguna = ? AND kod_status = '04' LIMIT 1");
        $stmt->execute([$_SESSION['id_pengguna']]);
        $app = $stmt->fetch();

        if (!$app) {
            $_SESSION['error'] = "Tiada surat tawaran ditemui atau permohonan anda belum diluluskan.";
            header("Location: ?page=dashboard");
            exit;
        }

        $id_permohonan = $app['id_permohonan'];

        $adminController = new AdminController();
        $detail = $adminController->getApplicationDetail($id_permohonan);

        $stmt = $pdo->prepare("SELECT jenis_dokumen, nama_fail FROM dokumen WHERE id_permohonan = ?");
        $stmt->execute([$id_permohonan]);
        $docsList = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $detail['dokumen_list'] = $docsList;

        require_once "app/helpers/SuratTawaranGenerator.php";
        $generator = new SuratTawaranGenerator($detail);
        $generator->generateLetter();
        $generator->Output('I', 'Surat_Tawaran_MTA.pdf');
        exit;

    case 'download_peraturan':

        AuthMiddleware::check();

        $filePath = 'public/assets/docs/peraturan_mta.pdf';
        if (!file_exists($filePath)) {
            $_SESSION['error'] = "Fail peraturan tidak ditemui.";
            header("Location: ?page=dashboard");
            exit;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Peraturan_MTA.pdf"');
        readfile($filePath);
        exit;

    case 'admin_cetak_surat_tawaran':

        AdminMiddleware::check();

        $id_permohonan = $_GET['id'] ?? 0;

        $adminController = new AdminController();
        $detail = $adminController->getApplicationDetail($id_permohonan);

        if (!$detail || ($detail['permohonan']['kod_status'] ?? '') !== '04') {
            $_SESSION['error'] = "Permohonan tidak sah atau belum diluluskan.";
            header("Location: ?page=admin_senarai");
            exit;
        }

        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT jenis_dokumen, nama_fail FROM dokumen WHERE id_permohonan = ?");
        $stmt->execute([$id_permohonan]);
        $docsList = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $detail['dokumen_list'] = $docsList;

        require_once "app/helpers/SuratTawaranGenerator.php";
        $generator = new SuratTawaranGenerator($detail);
        $generator->generateLetter();
        $generator->Output('I', 'Surat_Tawaran_MTA.pdf');
        exit;

    case 'admin_dashboard':

        AdminMiddleware::check();

        $adminController = new AdminController();

        $stats = $adminController->getStats();

        $content = "views/admin/dashboard.php";

        require_once "views/layouts/admin_layout.php";

        break;

    case 'admin_senarai':

        AdminMiddleware::check();

        $adminController = new AdminController();

        $filters = [
            'kod_status' => $_GET['kod_status'] ?? null,
            'carian' => $_GET['carian'] ?? null
        ];

        $applications = $adminController->getApplications($filters);

        $statusList = $adminController->getStatusList();

        $content = "views/admin/senarai.php";

        require_once "views/layouts/admin_layout.php";

        break;

    case 'admin_export_csv':

        AdminMiddleware::check();

        $adminController = new AdminController();

        $filters = [
            'kod_status' => $_GET['kod_status'] ?? null,
            'carian' => $_GET['carian'] ?? null
        ];

        $adminController->exportCSV($filters);

        exit;

    case 'admin_emails':

        AdminMiddleware::check();

        $content = "views/admin/emails.php";

        require_once "views/layouts/admin_layout.php";

        break;

    case 'admin_json_emel':

        AdminMiddleware::check();

        $id = $_GET['id'] ?? 0;
        
        require_once "app/helpers/EmailSimulator.php";
        $email = EmailSimulator::getEmailDetail($id);
        
        header('Content-Type: application/json');
        if (!$email) {
            echo json_encode(['error' => 'Emel tidak dijumpai.']);
        } else {
            echo json_encode($email);
        }
        exit;

    case 'admin_lihat_emel':

        AdminMiddleware::check();

        $id = $_GET['id'] ?? 0;
        
        require_once "app/helpers/EmailSimulator.php";
        $email = EmailSimulator::getEmailDetail($id);
        
        if (!$email) {
            echo "Emel tidak ditemui.";
        } else {
            echo $email['kandungan'];
        }
        exit;

    case 'admin_lihat':

        AdminMiddleware::check();

        $id_permohonan = $_GET['id'] ?? 0;

        $adminController = new AdminController();

        $detail = $adminController->getApplicationDetail($id_permohonan);

        $content = "views/admin/lihat.php";

        require_once "views/layouts/admin_layout.php";

        break;

    case 'admin_update_status':

        AdminMiddleware::check();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Sesi tidak sah. Sila muat semula halaman.";
            header("Location: ?page=admin_lihat&id=" . ($_GET['id'] ?? 0));
            exit;
        }

        $id_permohonan = $_GET['id'] ?? 0;
        $kod_status = $_POST['kod_status'] ?? '';
        $catatan = $_POST['catatan'] ?? '';
        $batch = $_POST['batch'] ?? null;

        $adminController = new AdminController();

        $result = $adminController->updateStatus(
            $id_permohonan,
            $kod_status,
            $catatan,
            $_SESSION['id_pengguna'],
            $batch
        );

        if ($result === true) {
            $_SESSION['success'] = "Status permohonan berjaya dikemaskini.";
        } else {
            $_SESSION['error'] = $result;
        }

        header("Location: ?page=admin_lihat&id=" . $id_permohonan);
        exit;

    case 'admin_logout':

        session_destroy();

        header("Location: ?page=admin_login");

        exit;

    default:

        echo "404 Page Not Found";

        break;
}

// =========================
// HELPER: VALIDATE PERMOHONAN SESSION
// =========================
function validatePermohonanSession() {
    if (!isset($_SESSION['id_permohonan']) || !isset($_SESSION['id_pengguna'])) {
        $_SESSION['error'] = "Sesi tidak sah. Sila mula permohonan baharu.";
        header("Location: ?page=dashboard");
        exit;
    }

    $id = (int) $_SESSION['id_permohonan'];

    if ($id <= 0) {
        unset($_SESSION['id_permohonan']);
        $_SESSION['error'] = "ID permohonan tidak sah. Sila mula permohonan baharu.";
        header("Location: ?page=dashboard");
        exit;
    }

    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT id_permohonan FROM permohonan 
        WHERE id_permohonan = ? AND id_pengguna = ? AND kod_status IN ('00', '08')
    ");
    $stmt->execute([$id, $_SESSION['id_pengguna']]);

    if (!$stmt->fetch()) {
        unset($_SESSION['id_permohonan']);
        $_SESSION['error'] = "Permohonan tidak sah atau telah dikemaskini. Sila mula permohonan baharu.";
        header("Location: ?page=dashboard");
        exit;
    }
}

// =========================
// HELPER: GET LANGKAH SEMASA
// =========================
function getLangkahSemasa($id_permohonan) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT langkah_semasa FROM permohonan WHERE id_permohonan = ?
    ");
    $stmt->execute([(int) $id_permohonan]);
    $step = $stmt->fetchColumn();
    return $step ? (int)$step : 1;
}