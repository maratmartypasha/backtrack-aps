<?php

if ($_SERVER['SERVER_NAME'] == "localhost") {
    // Local development
    define("ROOT", "http://localhost/music_website/public");

    define("DBDRIVER", "mysql");
    define("DBHOST", "localhost");
    define("DBUSER", "root");
    define("DBPASS", "");
    define("DBNAME", "music_db");
    define("DBPORT", "3306");

} else {
    // Railway deployment (menggunakan koneksi TCP dari MYSQL_PUBLIC_URL)
    define("ROOT", "https://" . $_SERVER['HTTP_HOST']);

    define("DBDRIVER", "mysql");
    define("DBHOST", "interchange.proxy.rlwy.net");
    define("DBUSER", "root");
    define("DBPASS", "zLnTKnGcWGZNosePKknHFagQrCQSuNsu");
    define("DBNAME", "railway");
    define("DBPORT", "51580");
}

// Koneksi database menggunakan PDO
try {
    $dsn = DBDRIVER . ":host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME;
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!"; // aktifkan jika ingin lihat status koneksi
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
