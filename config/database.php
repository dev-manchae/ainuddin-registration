<?php

function getConnection()
{

    $host = "localhost";
    $dbname = "ainuddin_registration";
    $username = "root";
    $password = "";

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