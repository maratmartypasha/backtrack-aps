<?php

if ($_SERVER['SERVER_NAME'] == "localhost") {
    define("ROOT", "http://localhost/music_website/public");

    define("DBDRIVER", "mysql");
    define("DBHOST", "localhost");
    define("DBUSER", "root");
    define("DBPASS", "");
    define("DBNAME", "music_db");
    define("DBPORT", "3306");

} else {
    define("ROOT", "https://" . $_SERVER['HTTP_HOST']);

    define("DBDRIVER", "mysql");
    define("DBHOST", getenv("MYSQLHOST"));
    define("DBUSER", getenv("MYSQLUSER"));
    define("DBPASS", getenv("MYSQLPASSWORD"));
    define("DBNAME", getenv("MYSQLDATABASE"));
    define("DBPORT", getenv("MYSQLPORT"));
}

// PDO connection
$host = DBHOST;
$port = DBPORT;
$dbname = DBNAME;
$user = DBUSER;
$pass = DBPASS;

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8";

$tries = 5;
while ($tries > 0) {
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break;
    } catch (PDOException $e) {
        $tries--;
        if ($tries == 0) {
            die("Database connection failed: " . $e->getMessage());
        }
        sleep(2);
    }
}
