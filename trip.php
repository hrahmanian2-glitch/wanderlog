<?php
require 'data.php';
require 'header.php';

if (!estConnecte()) {
    header('Location: login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$voyage = getTrip($id);

if ($voyage === null) {
    echo '<div class="error">Voyage introuvable.</div>';
    require 'footer.php';
    exit;
}

$auteur = getUser($voyage['email']);
$nomAuteur = $auteur ? ($auteur['prenom'] . ' ' . $auteur['nom']) : 'Inconnu';

$nbLikes = compterLikes($id);
$dejaLike = userALike(emailActuel(), $id);

$commentaires = getCommentairesDuTrip($id);
?>

<div class="container"><article class="trip-detail">
    <a href="index.php" class="back-link">← Retour à la carte</a>

    <h1><?= htmlspecialchars($voyage['titre']) ?></h1>

    <div class="trip-meta">
        par <a href="profile.php?email=<?= urlencode($voyage['email']) ?>" class="author-link"><strong><?= htmlspecialchars($nomAuteur) ?></strong></a>
        <?php if ($voyage['lieu']): ?>
            — <?= htmlspecialchars($voyage['lieu']) ?>
        <?php endif; ?>
        <?php if ($voyage['date']): ?>
            — <?= htmlspecialchars($voyage['date']) ?>
        <?php endif; ?>
    </div>

    <div class="trip-story">
        <?= nl2br(htmlspecialchars($voyage['histoire'])) ?>
    </div>

    <?php if (!empty($voyage['photos'])): ?>
        <div class="trip-photos">
            <?php foreach ($voyage['photos'] as $photo): ?>
                <img src="uploads/<?= htmlspecialchars($photo) ?>" alt="Photo de voyage">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="like-section">
        <button id="like-btn"
                class="like-btn <?= $dejaLike ? 'liked' : '' ?>"
                data-trip-id="<?= $id ?>">
            <span class="heart">♥</span>
            <span id="like-count"><?= $nbLikes ?></span>
            <span class="like-label">J'aime</span>
        </button>
    </div>

    <section class="comments-section">
        <h2>Commentaires (<span id="comment-count"><?= count($commentaires) ?></span>)</h2>

        <form id="comment-form" data-trip-id="<?= $id ?>">
            <textarea id="comment-input"
                      name="contenu"
                      placeholder="Ajouter un commentaire..."
                      rows="3"
                      maxlength="500"
                      required></textarea>
            <button type="submit">Publier</button>
        </form>

        <div id="comments-list">
            <?php foreach ($commentaires as $c): ?>
                <div class="comment" data-comment-id="<?= $c['id'] ?>">
                    <div class="comment-header">
                        <strong><?= htmlspecialchars($c['auteur']) ?></strong>
                        <span class="comment-date">
                            <?= date('d/m/Y à H:i', $c['created_at']) ?>
                        </span>
                    </div>
                    <p class="comment-content"><?= nl2br(htmlspecialchars($c['contenu'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</article></div>

<script src="js/trip.js"></script>

<?php require 'footer.php'; ?>
