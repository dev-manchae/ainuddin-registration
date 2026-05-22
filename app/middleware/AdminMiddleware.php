<?php

class AdminMiddleware {

    public static function check() {

        if (
            !isset($_SESSION['id_pengguna']) ||
            ($_SESSION['peranan'] ?? '') !== 'admin'
        ) {

            $_SESSION['error'] = "Akses ditolak. Sila log masuk sebagai admin.";

            header("Location: ?page=admin_login");
            exit;

        }

    }

}