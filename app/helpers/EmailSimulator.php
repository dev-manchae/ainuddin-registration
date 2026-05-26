<?php
require_once __DIR__ . '/../../config/database.php';

class EmailSimulator {

    private static $brandColor = '#1e5631';
    private static $brandName = 'Tahfiz Ainuddin';

    /**
     * Generate HTML email template and log to table
     */
    public static function simulate($id_permohonan, $recipient, $subject, $template, $variables = []) {
        $pdo = getConnection();
        
        $body = self::getTemplateHtml($template, $variables);
        
        $stmt = $pdo->prepare("
            INSERT INTO simulasi_emel (id_permohonan, penerima, subjek, kandungan)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$id_permohonan, $recipient, $subject, $body]);
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
     * Render the templates
     */
    private static function getTemplateHtml($template, $variables) {
        $headerColor = self::$brandColor;
        $brand = self::$brandName;
        
        $title = $variables['title'] ?? 'Notifikasi Sistem';
        $no_rujukan = $variables['no_rujukan'] ?? '-';
        $nama_pelajar = $variables['nama_pelajar'] ?? '-';
        $nama_penjaga = $variables['nama_penjaga'] ?? 'Penjaga';
        $catatan = $variables['catatan'] ?? '';
        $no_pelajar = $variables['no_pelajar'] ?? '';

        $contentHtml = '';

        switch ($template) {
            case 'pendaftaran_diterima':
                $contentHtml = "
                    <p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{$nama_penjaga}</strong>,</p>
                    <p>Terima kasih kerana berminat dengan {$brand}. Permohonan pendaftaran bagi anak/anak jagaan tuan/puan telah berjaya diterima dan kini sedang disemak oleh pihak pentadbiran.</p>
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='background: #f8fafc;'>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; width: 150px;'>No. Rujukan:</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; color: #1e5631; font-weight: 700;'>{$no_rujukan}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Nama Pelajar:</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'>{$nama_pelajar}</td>
                        </tr>
                        <tr style='background: #f8fafc;'>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Status:</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'><span style='background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;'>Permohonan Diterima</span></td>
                        </tr>
                    </table>
                    <p>Status permohonan boleh disemak dari semasa ke semasa dengan melog masuk ke Portal Pendaftaran Pelajar Tahfiz Ainuddin.</p>
                    <p>Sekiranya terdapat sebarang kemusykilan, sila hubungi pihak kami.</p>
                ";
                break;

            case 'permohonan_diluluskan':
                $contentHtml = "
                    <p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{$nama_penjaga}</strong>,</p>
                    <p style='color: #15803d; font-size: 16px; font-weight: 600;'>Tahniah! Permohonan pendaftaran anak/anak jagaan tuan/puan telah DILULUSKAN.</p>
                    <p>Butiran kemasukan pelajar rasmi adalah seperti berikut:</p>
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='background: #f8fafc;'>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; width: 150px;'>No. Pelajar:</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; color: #166534; font-weight: 700; font-size: 18px; letter-spacing: 0.5px;'>{$no_pelajar}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Nama Pelajar:</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'>{$nama_pelajar}</td>
                        </tr>
                        <tr style='background: #f8fafc;'>
                            <td style='padding: 10px; border: 1px solid #e2e8f0; font-weight: 600;'>Program:</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'>Tahfiz Sepenuh Masa (THFZ)</td>
                        </tr>
                    </table>
                    <p>Sila log masuk ke Portal Pendaftaran untuk memuat turun dokumen rasmi kemasukan:</p>
                    <ol style='padding-left: 20px; line-height: 1.6; color: #334155; margin-bottom: 20px;'>
                        <li><strong>Surat Tawaran Kemasukan</strong></li>
                        <li><strong>Surat Peraturan & Tatatertib Pelajar</strong></li>
                    </ol>
                    <p>Sila pastikan segala dokumen peraturan ditandatangani dan dibawa semasa hari pendaftaran fizikal.</p>
                ";
                break;

            case 'pembetulan_diperlukan':
                $catatanHtml = nl2br(htmlspecialchars($catatan));
                $contentHtml = "
                    <p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{$nama_penjaga}</strong>,</p>
                    <p style='color: #b45309; font-size: 16px; font-weight: 600;'>Pihak pentadbir memerlukan pembetulan/kemaskini maklumat permohonan bagi anak jagaan tuan/puan.</p>
                    <div style='background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                        <strong style='color: #b45309; display: block; margin-bottom: 6px;'>Catatan Pembetulan dari Pentadbir:</strong>
                        <span style='color: #78350f; font-style: italic;'>\"{$catatanHtml}\"</span>
                    </div>
                    <p>Sila log masuk ke Portal Pendaftaran Pelajar, klik pada butang <strong>\"Kemaskini\"</strong> pada permohonan berkenaan untuk melakukan pembetulan, dan hantar semula permohonan.</p>
                    <p>Langkah pembetulan segera amat dihargai bagi mengelakkan permohonan tertunda.</p>
                ";
                break;

            case 'permohonan_ditolak':
                $catatanHtml = !empty($catatan) ? nl2br(htmlspecialchars($catatan)) : 'Tiada catatan khusus.';
                $contentHtml = "
                    <p>Assalamualaikum wrt. wbt. Tuan/Puan <strong>{$nama_penjaga}</strong>,</p>
                    <p>Dukacita dimaklumkan bahawa permohonan pendaftaran kemasukan anak/anak jagaan tuan/puan ke {$brand} telah <strong>DITOLAK</strong> selepas melalui proses saringan penilaian.</p>
                    <div style='background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                        <strong style='color: #991b1b; display: block; margin-bottom: 6px;'>Sebab Penolakan / Catatan:</strong>
                        <span style='color: #991b1b;'>{$catatanHtml}</span>
                    </div>
                    <p>Pihak Maahad ingin merakamkan ucapan setinggi-tinggi penghargaan di atas minat tuan/puan. Kami mendoakan kejayaan cemerlang bagi anak jagaan tuan/puan dalam pengajian Al-Quran dan akademik di institusi lain.</p>
                ";
                break;
        }

        // Return unified email HTML wrapping wrapper
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
                .wrapper { width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
                .header { background-color: {$headerColor}; padding: 30px 20px; text-align: center; color: #ffffff; }
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
    }
}
