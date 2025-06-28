<?php

if ($_SERVER['SERVER_NAME'] == "localhost") {
    define("ROOT", "http://localhost/music_website/public");
    define("DBDRIVER", "mysql");
    define("DBHOST", "localhost");
    define("DBUSER", "root");
    define("DBPASS", "");
    define("DBNAME", "music_db");
    define("DBPORT", "3307");
} else {
    define("ROOT", "https://" . $_SERVER['HTTP_HOST']);
    define("DBDRIVER", "mysql");
    define("DBHOST", getenv("MYSQLHOST"));
    define("DBUSER", getenv("MYSQLUSER"));
    define("DBPASS", getenv("MYSQLPASSWORD"));
    define("DBNAME", getenv("MYSQLDATABASE"));
    define("DBPORT", getenv("MYSQLPORT"));
}

$maxRetries = 5;
$retryDelay = 2;
$attempt = 0;

while ($attempt < $maxRetries) {
    try {
        $dsn = DBDRIVER . ":host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME;
        $pdo = new PDO($dsn, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break;
    } catch (PDOException $e) {
        $attempt++;
        if ($attempt == $maxRetries) {
            die("Database connection failed: " . $e->getMessage());
        }
        sleep($retryDelay);
    }
}
