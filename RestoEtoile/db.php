<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'resto_etoile_db';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}
?>   