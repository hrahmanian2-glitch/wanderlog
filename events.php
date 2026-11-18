<?php
require 'data.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['ok' => false, 'erreur' => 'Non connecté']);
    exit;
}

$since = intval($_GET['since'] ?? 0);
$tripId = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : null;

$nouveauxVoyages = [];
foreach (getAllTrips() as $v) {
    if ($v['created_at'] > $since) {
        $auteur = getUser($v['email']);
        $nouveauxVoyages[] = [
            'id'       => $v['id'],
            'email'    => $v['email'],
            'titre'    => $v['titre'],
            'histoire' => $v['histoire'],
            'lat'      => $v['lat'],
            'lng'      => $v['lng'],
            'lieu'     => $v['lieu'],
            'date'     => $v['date'],
            'photos'   => $v['photos'],
            'auteur'   => $auteur ? ($auteur['prenom'] . ' ' . $auteur['nom']) : 'Inconnu'
        ];
    }
}

$nouveauxCommentaires = [];
if ($tripId !== null) {
    foreach (getCommentairesDuTrip($tripId) as $c) {
        if ($c['created_at'] > $since) {
            $nouveauxCommentaires[] = $c;
        }
    }
}

$likesData = null;
if ($tripId !== null) {
    $likesData = [
        'count' => compterLikes($tripId),
        'liked' => userALike($_SESSION['email'], $tripId)
    ];
}

echo json_encode([
    'ok'       => true,
    'now'      => time(),
    'trips'    => $nouveauxVoyages,
    'comments' => $nouveauxCommentaires,
    'likes'    => $likesData
]);
