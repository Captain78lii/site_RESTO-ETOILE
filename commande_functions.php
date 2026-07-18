<?php
// Résume les options d'une ligne de commande (viandes, sauces, suppléments, menu...) en texte lisible
function formatOptionsCommande($options_json) {
    if (!$options_json) return '';
    $options = json_decode($options_json, true);
    if (!$options) return '';

    $parties = [];
    if (!empty($options['viandes'])) $parties[] = 'Viandes : ' . implode(', ', $options['viandes']);
    if (!empty($options['pain'])) $parties[] = 'Pain : ' . $options['pain'];
    if (!empty($options['sauce'])) $parties[] = 'Sauce : ' . $options['sauce'];
    if (!empty($options['sauces'])) $parties[] = 'Sauces : ' . implode(', ', $options['sauces']);
    if (!empty($options['crudites'])) $parties[] = 'Crudités : ' . implode(', ', $options['crudites']);
    if (isset($options['ble'])) $parties[] = 'Blé : ' . $options['ble'] . ', Salade : ' . $options['salade'] . ', Frites : ' . $options['frites'];
    if (!empty($options['supplements'])) $parties[] = 'Suppléments : ' . implode(', ', $options['supplements']);
    if (!empty($options['menu'])) $parties[] = 'En Menu avec : ' . $options['menu'];

    return implode(' • ', array_map('htmlspecialchars', $parties));
}
