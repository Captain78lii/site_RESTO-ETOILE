<?php
// Protection CSRF : un jeton par session, à inclure dans tout formulaire ou lien qui modifie des données.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupère le jeton de la session en cours (le génère s'il n'existe pas encore)
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Champ caché à insérer dans tout formulaire POST qui modifie des données
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

// Ajoute le jeton CSRF à une URL d'action GET (ex: lien "Supprimer", "Confirmer"...)
function csrf_url($url) {
    $separateur = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $separateur . 'csrf_token=' . urlencode(csrf_token());
}

// Vérifie le jeton reçu (POST, GET, ou en-tête X-CSRF-Token pour les appels fetch en JSON) ; arrête l'exécution si absent ou invalide
function csrf_verify() {
    $recu = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $attendu = $_SESSION['csrf_token'] ?? '';
    if (!$attendu || !hash_equals($attendu, $recu)) {
        http_response_code(403);
        die("Requête invalide (jeton de sécurité manquant ou expiré). Retournez en arrière et réessayez.");
    }
}
