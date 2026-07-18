<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

if (isset($_SESSION['user_id'])) {
    csrf_verify();

    $user_id = intval($_SESSION['user_id']);
    // CASCADE supprime aussi ses avis/réservations
    mysqli_query($conn, "DELETE FROM utilisateurs WHERE id = $user_id");

    session_destroy();
    header("Location: /index.php?msg=compte_supprime");
    exit();
}