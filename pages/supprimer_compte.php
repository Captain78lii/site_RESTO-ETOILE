<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

if (isset($_SESSION['user_id'])) {
    csrf_verify();

    $user_id = intval($_SESSION['user_id']);
    // supprime l'utilisateur, supprime aussi ses avis/réservations car clés étrangères en CASCADE)
    mysqli_query($conn, "DELETE FROM utilisateurs WHERE id = $user_id");

    session_destroy(); // déconnecte
    header("Location: /index.php?msg=compte_supprime");
    exit();
}