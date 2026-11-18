<?php
require 'data.php';
require 'header.php';

if (!estConnecte()) {
    header('Location: login.php');
    exit;
}

$emailProfil = $_GET['email'] ?? emailActuel();

$user = getUser($emailProfil);

if ($user === null) {
    echo '<div class="error">Utilisateur introuvable.</div>';
    require 'footer.php';
    exit;
}

$tousLesVoyages = getAllTrips();
$voyagesUser = [];
foreach ($tousLesVoyages as $v) {
    if ($v['email'] === $emailProfil) {
        $voyagesUser[] = $v;
    }
}

usort($voyagesUser, function($a, $b) {
    return $b['created_at'] - $a['created_at'];
});

$estMonProfil = ($emailProfil === emailActuel());
?>

<div class="container"><div class="profile-header">
    <div class="profile-avatar">
        <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
    </div>
    <div class="profile-info">
        <h1><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h1>
        <p class="profile-stats">
            <strong><?= count($voyagesUser) ?></strong> voyage<?= count($voyagesUser) > 1 ? 's' : '' ?> partagé<?= count($voyagesUser) > 1 ? 's' : '' ?>
        </p>
        <?php if ($estMonProfil): ?>
            <a href="add_trip.php" class="btn-primary">+ Partager un voyage</a>
        <?php endif; ?>
    </div>
</div>

<?php if (count($voyagesUser) === 0): ?>
    <div class="empty-state">
        <p>
            <?php if ($estMonProfil): ?>
                Tu n'as pas encore partagé de voyage.
                <a href="add_trip.php">Commence par en partager un !</a>
            <?php else: ?>
                <?= htmlspecialchars($user['prenom']) ?> n'a pas encore partagé de voyage.
            <?php endif; ?>
        </p>
    </div>
<?php else: ?>
    <div class="trips-grid">
        <?php foreach ($voyagesUser as $v): ?>
            <a href="trip.php?id=<?= $v['id'] ?>" class="trip-card">
                <?php if (!empty($v['photos'])): ?>
                    <div class="trip-card-photo">
                        <img src="uploads/<?= htmlspecialchars($v['photos'][0]) ?>" alt="">
                    </div>
                <?php else: ?>
                    <div class="trip-card-photo trip-card-no-photo"></div>
                <?php endif; ?>
                <div class="trip-card-body">
                    <h3><?= htmlspecialchars($v['titre']) ?></h3>
                    <?php if ($v['lieu']): ?>
                        <p class="trip-card-lieu"><?= htmlspecialchars($v['lieu']) ?></p>
                    <?php endif; ?>
                    <?php if ($v['date']): ?>
                        <p class="trip-card-date"><?= htmlspecialchars($v['date']) ?></p>
                    <?php endif; ?>
                    <p class="trip-card-stats">
                        <?= compterLikes($v['id']) ?> j'aime
                        &middot; <?= count(getCommentairesDuTrip($v['id'])) ?> commentaire<?= count(getCommentairesDuTrip($v['id'])) > 1 ? 's' : '' ?>
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div><?php require 'footer.php'; ?>
