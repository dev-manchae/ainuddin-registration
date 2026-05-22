<?php

class AuthMiddleware {

    public static function check() {

        if (!isset($_SESSION['id_pengguna'])) {

            header("Location: ?page=login");
            exit;

        }

    }

}