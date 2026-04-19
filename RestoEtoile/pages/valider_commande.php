<?php
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php');
session_start();

// On vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour commander.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// On ajoute 1 point de fidélité à l'utilisateur
$query = "UPDATE utilisateurs SET points_fidelite = points_fidelite + 1 WHERE id = '$user_id'";

if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Commande validée ! +1 point de fidélité ajouté.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la validation.']);
}
?>