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
    // Railway deployment
    define("ROOT", "https://" . $_SERVER['HTTP_HOST']); // gunakan domain Railway otomatis

    define("DBDRIVER", "mysql");
    define("DBHOST", getenv("DB_HOST"));
    define("DBUSER", getenv("DB_USER"));
    define("DBPASS", getenv("DB_PASS"));
    define("DBNAME", getenv("DB_NAME"));
    define("DBPORT", getenv("DB_PORT"));
}
