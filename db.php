<?php
include_once(__DIR__ . '/csrf_functions.php');

// En local (XAMPP), ces variables d'environnement n'existent pas : on retombe
// sur les identifiants XAMPP par défaut. Sur Railway, elles sont fournies par
// le service MySQL ajouté au projet.
$host   = getenv('MYSQLHOST') ?: 'localhost';
$user   = getenv('MYSQLUSER') ?: 'root';
$pass   = getenv('MYSQLPASSWORD') ?: '';
$dbname = getenv('MYSQLDATABASE') ?: 'resto_etoile_db';
$port   = getenv('MYSQLPORT') ?: 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}
