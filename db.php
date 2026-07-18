<?php
include_once(__DIR__ . '/csrf_functions.php');

// en local (XAMPP) ces variables n'existent pas -> fallback sur les défauts XAMPP
$host   = getenv('MYSQLHOST') ?: 'localhost';
$user   = getenv('MYSQLUSER') ?: 'root';
$pass   = getenv('MYSQLPASSWORD') ?: '';
$dbname = getenv('MYSQLDATABASE') ?: 'resto_etoile_db';
$port   = getenv('MYSQLPORT') ?: 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}
