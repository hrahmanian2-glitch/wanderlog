<?php
require 'data.php';
require 'header.php';

if (!estConnecte()) {
    header('Location: login.php');
    exit;
}

$message = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'trip_ajoute') {
    $message = 'Ton voyage a été partagé !';
}

$voyages = getAllTrips();

$voyagesPourJS = [];
foreach ($voyages as $v) {
    $auteur = getUser($v['email']);
    $voyagesPourJS[] = [
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
?>

<div class="map-topbar">
    <div class="map-topbar-left">
        <span class="map-title">Carte des voyages</span>
        <span class="map-count"><?= count($voyages) ?> voyage<?= count($voyages) > 1 ? 's' : '' ?></span>
    </div>
    <div class="map-topbar-right">
        <?php if ($message !== ''): ?>
            <div class="success map-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <a href="add_trip.php" class="btn-primary">+ Partager un voyage</a>
    </div>
</div>

<div id="map"></div>

<script>
    const VOYAGES = <?= json_encode($voyagesPourJS, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/map.js"></script>

<?php require 'footer.php'; ?>
