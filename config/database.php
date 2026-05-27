<?php

function getConnection()
{

    // Retrieve credentials from environment variables for production, with fallbacks for local XAMPP
    $host = getenv('DB_HOST') ?: "localhost";
    $dbname = getenv('DB_NAME') ?: "ainuddin_registration";
    $username = getenv('DB_USER') ?: "root";
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";

    try {

        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;

    } catch (PDOException $e) {

        error_log("Database Connection Error: " . $e->getMessage());
        die("Sistem mengalami ralat teknikal. Sila cuba seketika lagi.");

    }

}