<?php
// Gère l'upload d'une image produit envoyée depuis un formulaire admin (ajout ou modification).
//
// $nom_champ      : nom du <input type="file"> dans le formulaire
// $image_courante : nom de fichier déjà en base à conserver si aucun nouveau fichier n'est envoyé (mode édition)
//
// Retourne :
//   - le nom du fichier généré (string) en cas d'upload réussi
//   - $image_courante si aucun fichier n'a été envoyé
//   - ['erreur' => "..."] si le fichier envoyé est invalide
function handle_image_upload($nom_champ, $image_courante = null) {
    if (!isset($_FILES[$nom_champ]) || $_FILES[$nom_champ]['error'] === UPLOAD_ERR_NO_FILE) {
        return $image_courante;
    }

    $fichier = $_FILES[$nom_champ];

    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        return ['erreur' => "Erreur lors de l'envoi du fichier."];
    }

    $taille_max = 5 * 1024 * 1024; // 5 Mo
    if ($fichier['size'] > $taille_max) {
        return ['erreur' => "L'image dépasse la taille maximale de 5 Mo."];
    }

    $extensions_autorisees = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    if (!array_key_exists($extension, $extensions_autorisees)) {
        return ['erreur' => "Format d'image non supporté (jpg, png, gif ou webp uniquement)."];
    }

    // Vérifie le vrai type MIME du contenu (pas seulement l'extension du nom de fichier),
    // pour empêcher qu'un fichier malveillant déguisé en .jpg soit accepté.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_reel = finfo_file($finfo, $fichier['tmp_name']);
    finfo_close($finfo);

    if ($mime_reel !== $extensions_autorisees[$extension]) {
        return ['erreur' => "Le fichier envoyé n'est pas une image valide."];
    }

    // Nom de fichier généré côté serveur : jamais le nom fourni par l'utilisateur (évite toute tentative d'écrasement ou de traversée de chemin)
    $nom_fichier = uniqid('produit_', true) . '.' . $extension;
    $chemin_destination = __DIR__ . '/images/' . $nom_fichier;

    if (!move_uploaded_file($fichier['tmp_name'], $chemin_destination)) {
        return ['erreur' => "Impossible d'enregistrer l'image sur le serveur."];
    }

    return $nom_fichier;
}
