<?php
session_start(); // récupère la session actuelle
session_unset(); // vide les variables de session
session_destroy(); 

// redirige vers l'accueil
header("Location: /index.php");
exit();
?>