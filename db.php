<?php
include_once(__DIR__ . '/csrf_functions.php');

// priorité : config locale (hébergement mutualisé type InfinityFree, jamais commitée)
// > variables d'environnement (Railway) > défauts XAMPP en local
if (file_exists(__DIR__ . '/db.config.php')) {
    require __DIR__ . '/db.config.php';
} else {
    $host   = getenv('MYSQLHOST') ?: 'localhost';
    $user   = getenv('MYSQLUSER') ?: 'root';
    $pass   = getenv('MYSQLPASSWORD') ?: '';
    $dbname = getenv('MYSQLDATABASE') ?: 'resto_etoile_db';
    $port   = getenv('MYSQLPORT') ?: 3306;
}

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
