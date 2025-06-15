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
    define("DBHOST", getenv("DBHOST"));
    define("DBUSER", getenv("DBUSER"));
    define("DBPASS", getenv("DBPASS"));
    define("DBNAME", getenv("DBNAME"));
    define("DBPORT", getenv("DBPORT"));
}
