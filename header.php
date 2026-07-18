<?php
// démarre la session avant tout code HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resto Etoile</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <nav>
    <div class="logo">Resto Etoile</div>
    <ul>
        <?php if (isset($page_simple) && $page_simple == true): ?>
            <li><a href="/index.php">Retour à l'accueil</a></li>
        <?php else: ?>
            <li><a href="/index.php">Accueil</a></li>
            <li><a href="/pages/produits.php">La Carte</a></li>
            <li><a href="/pages/reservation.php">Réservation</a></li>
            <li><a href="/pages/panier.php" class="cart-link">Panier </a></li>
            <li><a href="/pages/avis.php">Avis</a></li> 
            <li><a href="/pages/propos.php">À Propos</a></li>
            <li><a href="/pages/contact.php">Contact</a></li>
            <li><a href="/pages/galerie.php">Galerie</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="/pages/admin.php" style="color:var(--accent-gold)">⚙️ Admin</a></li>
                <?php endif; ?>
                <li><a href="/pages/profil.php" style="color:var(--accent-gold)">👤 <?php echo htmlspecialchars($_SESSION['user_nom']); ?></a></li>
                <li><a href="/pages/logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="/pages/login.php">Connexion</a></li>
            <?php endif; ?>

        <?php endif; ?>
    </ul>
</nav>