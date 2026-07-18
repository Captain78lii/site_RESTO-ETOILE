<?php
// protection CSRF : un jeton par session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

// pour les liens GET qui modifient des données (ex: "Supprimer")
function csrf_url($url) {
    $separateur = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $separateur . 'csrf_token=' . urlencode(csrf_token());
}

// accepte aussi le header X-CSRF-Token pour les appels fetch en JSON
function csrf_verify() {
    $recu = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $attendu = $_SESSION['csrf_token'] ?? '';
    if (!$attendu || !hash_equals($attendu, $recu)) {
        http_response_code(403);
        die("Requête invalide (jeton de sécurité manquant ou expiré). Retournez en arrière et réessayez.");
    }
}
