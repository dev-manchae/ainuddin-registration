<?php
require_once __DIR__ . '/../../config/database.php';

class EmailSimulator {

    private static $brandColor = '#1e5631';
    private static $brandName = 'Tahfiz Ainuddin';

    /**
     * Generate HTML email template and log to table
     */
    public static function simulate($id_permohonan, $recipient, $subject, $template, $variables = [], $pdo = null) {
        if ($pdo === null) {
            $pdo = getConnection();
        }
        
        $dbTemplate = null;
        try {
            $stmt = $pdo->prepare("SELECT subject, content FROM email_templates WHERE template_key = ?");
            $stmt->execute([$template]);
            $dbTemplate = $stmt->fetch();
        } catch (PDOException $e) {
            // Fallback quietly if table is missing or query fails
        }

        $title = $variables['title'] ?? 'Notifikasi Sistem';
        $no_rujukan = $variables['no_rujukan'] ?? '-';
        $nama_pelajar = $variables['nama_pelajar'] ?? '-';
        $nama_penjaga = $variables['nama_penjaga'] ?? 'Penjaga';
        $catatan = $variables['catatan'] ?? '';
        $no_pelajar = $variables['no_pelajar'] ?? '';
        $brand = self::$brandName;

        if ($dbTemplate) {
            $subjectTemplate = $dbTemplate['subject'];
            $bodyTemplate = $dbTemplate['content'];
        } else {
            $fallback = self::getFallbackTemplate($template);
            $subjectTemplate = $fallback['subject'] ?? $subject;
            $bodyTemplate = $fallback['content'] ?? '';
        }

        $placeholders = [
            '{nama_penjaga}' => $nama_penjaga,
            '{nama_pelajar}' => $nama_pelajar,
            '{no_rujukan}' => $no_rujukan,
            '{no_pelajar}' => $no_pelajar,
            '{catatan}' => nl2br(htmlspecialchars($catatan)),
            '{brand}' => $brand
        ];

        $finalSubject = str_replace(array_keys($placeholders), array_values($placeholders), $subjectTemplate);
        $contentHtml = str_replace(array_keys($placeholders), array_values($placeholders), $bodyTemplate);

        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
                .wrapper { width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
                .header { background-color: " . self::$brandColor . "; padding: 30px 20px; text-align: center; color: #ffffff; }
                .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
                .header p { margin: 5px 0 0; opacity: 0.85; font-size: 13px; letter-spacing: 0.5px; }
                .content { padding: 35px 30px; font-size: 14px; line-height: 1.6; }
                .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
                .footer p { margin: 5px 0; }
            </style>
        </head>
        <body>
            <div class='wrapper'>
                <div class='header'>
                    <h1>{$brand}</h1>
                    <p>{$title}</p>
                </div>
                <div class='content'>
                    {$contentHtml}
                    <br>
                    <p>Sekian, terima kasih.</p>
                    <p><strong>Pihak Pentadbiran</strong><br>{$brand}</p>
                </div>
                <div class='footer'>
                    <p>Emel ini dihantar secara automatik melalui Sistem Pendaftaran Pelajar Tahfiz Ainuddin.</p>
                    <p>&copy; " . date('Y') . " {$brand}. Hak Cipta Terpelihara.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $stmt = $pdo->prepare("
            INSERT INTO simulasi_emel (id_permohonan, penerima, subjek, kandungan)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$id_permohonan, $recipient, $finalSubject, $body]);
        return true;
    }

    /**
     * Get email list for log view
     */
    public static function getEmails() {
        $pdo = getConnection();
        $stmt = $pdo->query("
            SELECT se.*, p.no_rujukan, pl.nama_penuh as nama_pelajar
            FROM simulasi_emel se
            LEFT JOIN permohonan p ON se.id_permohonan = p.id_permohonan
            LEFT JOIN pelajar pl ON p.id_permohonan = pl.id_permohonan
            ORDER BY se.tarikh_hantar DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get single email detail
     */
    public static function getEmailDetail($id_emel) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT se.*, p.no_rujukan, pl.nama_penuh as nama_pelajar
            FROM simulasi_emel se
            LEFT JOIN permohonan p ON se.id_permohonan = p.id_permohonan
            LEFT JOIN pelajar pl ON p.id_permohonan = pl.id_permohonan
            WHERE se.id_emel = ?
        ");
        $stmt->execute([$id_emel]);
        return $stmt->fetch();
    }

    /**
     * Fallback templates in case database entries are missing
     */
    private static function getFallbackTemplate($template) {
        $brand = self::$brandName;
        switch ($template) {
            case 'pendaftaran_diterima':
                return [
                    'subject' => 'Permohonan Pendaftaran Tahfiz Ainuddin Diterima - {no_rujukan}',
                    'content' => "<p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{nama_penjaga}</strong>,</p>\n" .
                                 "<p>Terima kasih kerana berminat dengan {brand}. Permohonan pendaftaran bagi anak/anak jagaan tuan/puan telah berjaya diterima dan kini sedang disemak oleh pihak pentadbiran.</p>\n" .
                                 "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>\n" .
                                 "    <tr style='background: #f8fafc;'>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; width: 150px;'>No. Rujukan:</td>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; color: #1e5631; font-weight: 700;'>{no_rujukan}</td>\n" .
                                 "    </tr>\n" .
                                 "    <tr>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Nama Pelajar:</td>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0;'>{nama_pelajar}</td>\n" .
                                 "    </tr>\n" .
                                 "    <tr style='background: #f8fafc;'>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Status:</td>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0;'><span style='background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;'>Permohonan Diterima</span></td>\n" .
                                 "    </tr>\n" .
                                 "</table>\n" .
                                 "<p>Status permohonan boleh disemak dari semasa ke semasa dengan melog masuk ke Portal Pendaftaran Pelajar Tahfiz Ainuddin.</p>\n" .
                                 "<p>Sekiranya terdapat sebarang kemusykilan, sila hubungi pihak kami.</p>"
                ];
            case 'permohonan_diluluskan':
                return [
                    'subject' => 'Tawaran Kemasukan Tahfiz Ainuddin - LULUS - {no_rujukan}',
                    'content' => "<p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{nama_penjaga}</strong>,</p>\n" .
                                 "<p style='color: #15803d; font-size: 16px; font-weight: 600;'>Tahniah! Permohonan pendaftaran anak/anak jagaan tuan/puan telah DILULUSKAN.</p>\n" .
                                 "<p>Butiran kemasukan pelajar rasmi adalah seperti berikut:</p>\n" .
                                 "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>\n" .
                                 "    <tr style='background: #f8fafc;'>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; width: 150px;'>No. Pelajar:</td>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; color: #166534; font-weight: 700; font-size: 18px; letter-spacing: 0.5px;'>{no_pelajar}</td>\n" .
                                 "    </tr>\n" .
                                 "    <tr>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Nama Pelajar:</td>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0;'>{nama_pelajar}</td>\n" .
                                 "    </tr>\n" .
                                 "    <tr style='background: #f8fafc;'>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Program:</td>\n" .
                                 "        <td style='padding: 10px; border: 1px solid #e2e8f0;'>Tahfiz Sepenuh Masa (THFZ)</td>\n" .
                                 "    </tr>\n" .
                                 "</table>\n" .
                                 "<p>Sila log masuk ke Portal Pendaftaran untuk memuat turun dokumen rasmi kemasukan:</p>\n" .
                                 "<ol style='padding-left: 20px; line-height: 1.6; color: #334155; margin-bottom: 20px;'>\n" .
                                 "    <li><strong>Surat Tawaran Kemasukan</strong></li>\n" .
                                 "    <li><strong>Surat Peraturan & Tatatertib Pelajar</strong></li>\n" .
                                 "</ol>\n" .
                                 "<p>Sila pastikan segala dokumen peraturan ditandatangani dan dibawa semasa hari pendaftaran fizikal.</p>"
                ];
            case 'pembetulan_diperlukan':
                return [
                    'subject' => 'Pembetulan Maklumat Diperlukan bagi Permohonan - {no_rujukan}',
                    'content' => "<p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{nama_penjaga}</strong>,</p>\n" .
                                 "<p style='color: #b45309; font-size: 16px; font-weight: 600;'>Pihak pentadbir memerlukan pembetulan/kemaskini maklumat permohonan bagi anak jagaan tuan/puan.</p>\n" .
                                 "<div style='background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 20px 0;'>\n" .
                                 "    <strong style='color: #b45309; display: block; margin-bottom: 6px;'>Catatan Pembetulan dari Pentadbir:</strong>\n" .
                                 "    <span style='color: #78350f; font-style: italic;'>\"{catatan}\"</span>\n" .
                                 "</div>\n" .
                                 "<p>Sila log masuk ke Portal Pendaftaran Pelajar, klik pada butang <strong>\"Kemaskini\"</strong> pada permohonan berkenaan untuk melakukan pembetulan, dan hantar semula permohonan.</p>\n" .
                                 "<p>Langkah pembetulan segera amat dihargai bagi mengelakkan permohonan tertunda.</p>"
                ];
            case 'permohonan_ditolak':
                return [
                    'subject' => 'Keputusan Permohonan Pendaftaran Tahfiz Ainuddin - {no_rujukan}',
                    'content' => "<p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{nama_penjaga}</strong>,</p>\n" .
                                 "<p>Dukacita dimaklumkan bahawa permohonan pendaftaran kemasukan anak/anak jagaan tuan/puan ke {brand} telah <strong>DITOLAK</strong> selepas melalui proses saringan penilaian.</p>\n" .
                                 "<div style='background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 6px; margin: 20px 0;'>\n" .
                                 "    <strong style='color: #991b1b; display: block; margin-bottom: 6px;'>Sebab Penolakan / Catatan:</strong>\n" .
                                 "    <span style='color: #991b1b;'>{catatan}</span>\n" .
                                 "</div>\n" .
                                 "<p>Pihak Maahad ingin merakamkan ucapan setinggi-tinggi penghargaan di atas minat tuan/puan. Kami mendoakan kejayaan cemerlang bagi anak jagaan tuan/puan dalam pengajian Al-Quran dan akademik di institusi lain.</p>"
                ];
            default:
                return ['subject' => '', 'content' => ''];
        }
    }
}
