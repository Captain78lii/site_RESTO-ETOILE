<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    csrf_verify();

    $id_res = intval($_GET['id']);
    $user_id = intval($_SESSION['user_id']);

    // vérifie que la réservation appartient bien à l'utilisateur
    $query = "DELETE FROM reservations WHERE id = $id_res AND user_id = $user_id";
    mysqli_query($conn, $query);
}

header("Location: profil.php");
exit();
?>