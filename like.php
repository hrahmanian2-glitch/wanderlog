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

if (getTrip($tripId) === null) {
    echo json_encode(['ok' => false, 'erreur' => 'Voyage introuvable']);
    exit;
}

$nouveauEtat = toggleLike($_SESSION['email'], $tripId);
$nouveauCompte = compterLikes($tripId);

echo json_encode([
    'ok'    => true,
    'liked' => $nouveauEtat,
    'count' => $nouveauCompte
]);
