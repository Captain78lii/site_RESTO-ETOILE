<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
header('Content-Type: application/json');

// vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour commander.']);
    exit;
}

// Vérifie le jeton CSRF envoyé via l'en-tête X-CSRF-Token (l'appel se fait en fetch/JSON, pas via un <form>)
$jeton_recu = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$jeton_attendu = $_SESSION['csrf_token'] ?? '';
if (!$jeton_attendu || !hash_equals($jeton_attendu, $jeton_recu)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Session expirée, veuillez rafraîchir la page et réessayer.']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Prix des suppléments et du passage en Menu : dupliqués côté serveur (voir pages/produits.php)
// pour ne jamais faire confiance au prix envoyé par le navigateur.
$SUPPLEMENTS_PRIX = [
    'Cheddar' => 0.50,
    'Œuf' => 0.50,
    'Olives' => 0.50,
    'Chèvre' => 1.00,
    'Piment' => 0.50,
    'Boursin' => 1.00,
    'Supplément Viande' => 2.00,
];
$PRIX_MENU = 1.00;

$panier = json_decode(file_get_contents('php://input'), true);

if (!is_array($panier) || count($panier) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Votre panier est vide.']);
    exit;
}

// Recalcule chaque ligne à partir du prix en base + des règles de suppléments/menu,
// afin qu'un panier trafiqué côté client ne puisse pas changer le prix payé.
$lignes = [];
$total = 0;

foreach ($panier as $item) {
    $produit_id = isset($item['id']) ? intval($item['id']) : 0;
    $quantite = isset($item['qty']) ? intval($item['qty']) : 0;

    if ($produit_id <= 0 || $quantite <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Article de panier invalide.']);
        exit;
    }

    $res = mysqli_query($conn, "SELECT nom, prix FROM produits WHERE id = $produit_id");
    $produit = $res ? mysqli_fetch_assoc($res) : null;

    if (!$produit) {
        echo json_encode(['status' => 'error', 'message' => "Un article de votre panier n'existe plus à la carte."]);
        exit;
    }

    $prix_unitaire = floatval($produit['prix']);
    $options = isset($item['options']) && is_array($item['options']) ? $item['options'] : null;

    if ($options) {
        if (!empty($options['supplements']) && is_array($options['supplements'])) {
            foreach ($options['supplements'] as $supplement) {
                $prix_unitaire += $SUPPLEMENTS_PRIX[$supplement] ?? 0;
            }
        }
        if (!empty($options['menu'])) {
            $prix_unitaire += $PRIX_MENU;
        }
    }

    $lignes[] = [
        'produit_id' => $produit_id,
        'nom_produit' => $produit['nom'],
        'prix_unitaire' => $prix_unitaire,
        'quantite' => $quantite,
        'options_json' => $options ? json_encode($options, JSON_UNESCAPED_UNICODE) : null,
    ];

    $total += $prix_unitaire * $quantite;
}

mysqli_begin_transaction($conn);

try {
    $stmt = mysqli_prepare($conn, "INSERT INTO commandes (user_id, total, statut) VALUES (?, ?, 'En attente')");
    mysqli_stmt_bind_param($stmt, "id", $user_id, $total);
    mysqli_stmt_execute($stmt);
    $commande_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "INSERT INTO commande_lignes (commande_id, produit_id, nom_produit, prix_unitaire, quantite, options_json) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($lignes as $ligne) {
        mysqli_stmt_bind_param(
            $stmt,
            "iisdis",
            $commande_id,
            $ligne['produit_id'],
            $ligne['nom_produit'],
            $ligne['prix_unitaire'],
            $ligne['quantite'],
            $ligne['options_json']
        );
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);

    // +1 point de fidélité par commande validée
    mysqli_query($conn, "UPDATE utilisateurs SET points_fidelite = points_fidelite + 1 WHERE id = $user_id");

    mysqli_commit($conn);

    echo json_encode([
        'status' => 'success',
        'message' => 'Commande validée ! +1 point de fidélité ajouté.',
        'commande_id' => $commande_id,
        'total' => number_format($total, 2, '.', ''),
    ]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la validation de la commande.']);
}
