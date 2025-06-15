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
    // Railway deployment - GUNAKAN koneksi internal
    define("ROOT", "https://" . $_SERVER['HTTP_HOST']);

    define("DBDRIVER", "mysql");
    define("DBHOST", getenv("MYSQLHOST"));         // mysql.railway.internal
    define("DBUSER", getenv("MYSQLUSER"));         // root
    define("DBPASS", getenv("MYSQLPASSWORD"));     // zLnTKnGcWGZNosePKknHFagQrCQSuNsu
    define("DBNAME", getenv("MYSQLDATABASE"));     // railway
    define("DBPORT", getenv("MYSQLPORT"));         // 3306
}

// PDO connection
try {
    $dsn = DBDRIVER . ":host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME;
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
