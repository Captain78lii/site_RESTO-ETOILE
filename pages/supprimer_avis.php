<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    csrf_verify();

    $id_avis = intval($_GET['id']);
    $user_id = intval($_SESSION['user_id']);

    // supprime que si l'avis appartient à l'utilisateur connecté
    $query = "DELETE FROM avis WHERE id = $id_avis AND user_id = $user_id";
    mysqli_query($conn, $query);
}
header("Location: profil.php");
exit();