<?php
session_start(); // On récupère la session actuelle
session_unset(); // On vide les variables de session
session_destroy(); // On détruit la session

// On redirige vers l'accueil
header("Location: /RestoEtoile/index.php");
exit();
?>