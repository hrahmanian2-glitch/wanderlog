<?php

require 'data.php';
session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['email'])) {
    echo json_encode(['ok' => false, 'erreur' => 'Non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'erreur' => 'Méthode invalide']);
    exit;
}

$tripId = intval($_POST['trip_id'] ?? 0);
$contenu = trim($_POST['contenu'] ?? '');

if ($contenu === '') {
    echo json_encode(['ok' => false, 'erreur' => 'Commentaire vide']);
    exit;
}

if (strlen($contenu) > 500) {
    echo json_encode(['ok' => false, 'erreur' => 'Commentaire trop long (max 500 caractères)']);
    exit;
}

if (getTrip($tripId) === null) {
    echo json_encode(['ok' => false, 'erreur' => 'Voyage introuvable']);
    exit;
}


$commentaire = ajouterCommentaire($_SESSION['email'], $tripId, $contenu);

echo json_encode([
    'ok'          => true,
    'commentaire' => $commentaire
]);
