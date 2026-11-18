<?php
require 'data.php';
require 'header.php';

if (!estConnecte()) {
    header('Location: login.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre    = trim($_POST['titre']    ?? '');
    $histoire = trim($_POST['histoire'] ?? '');
    $lieu     = trim($_POST['lieu']     ?? '');
    $date     = trim($_POST['date']     ?? '');
    $lat      = $_POST['lat'] ?? '';
    $lng      = $_POST['lng'] ?? '';

    if ($titre === '' || $histoire === '' || $lat === '' || $lng === '') {
        $erreur = 'Le titre, l\'histoire et l\'emplacement sont obligatoires.';
    } else if (!is_numeric($lat) || !is_numeric($lng)) {
        $erreur = 'Coordonnées invalides.';
    } else {
        $photosEnregistrees = [];

        if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {

            $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $tailleMax = 5 * 1024 * 1024;

            
            $nbFichiers = count($_FILES['photos']['name']);
            for ($i = 0; $i < $nbFichiers; $i++) {

                if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $nomOriginal = $_FILES['photos']['name'][$i];
                $tmpPath     = $_FILES['photos']['tmp_name'][$i];
                $taille      = $_FILES['photos']['size'][$i];


                if ($taille > $tailleMax) {
                    $erreur = 'Une photo dépasse 5 Mo.';
                    break;
                }

                $extension = strtolower(pathinfo($nomOriginal, PATHINFO_EXTENSION));
                if (!in_array($extension, $extensionsAutorisees)) {
                    $erreur = 'Type de fichier non autorisé : ' . $extension;
                    break;
                }


                $nomUnique = uniqid('img_', true) . '.' . $extension;
                $cheminFinal = $DOSSIER_UPLOADS . '/' . $nomUnique;

                if (move_uploaded_file($tmpPath, $cheminFinal)) {
                    $photosEnregistrees[] = $nomUnique;
                } else {
                    $erreur = 'Erreur lors de l\'enregistrement d\'une photo.';
                    break;
                }
            }
        }


        if ($erreur === '') {
            ajouterTrip(
                emailActuel(),
                $titre,
                $histoire,
                $lat,
                $lng,
                $lieu,
                $date,
                $photosEnregistrees
            );

            header('Location: index.php?msg=trip_ajoute');
            exit;
        }
    }
}
?>

<div class="container"><div class="form-card form-card-large">
    <h1>Partager un voyage</h1>
    <p class="subtitle">Clique sur la carte pour choisir l'emplacement, puis remplis le formulaire.</p>

    <?php if ($erreur !== ''): ?>
        <div class="error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div id="map-picker" style="height: 350px; border-radius: 8px; margin-bottom: 1rem;"></div>
    <p id="coord-display" class="coord-display">Aucun emplacement sélectionné.</p>

    <form method="post" action="add_trip.php" enctype="multipart/form-data">

        <input type="hidden" name="lat" id="input-lat">
        <input type="hidden" name="lng" id="input-lng">

        <label>
            Titre du voyage
            <input type="text" name="titre" required maxlength="100" placeholder="Ex: Weekend à Rome">
        </label>

        <label>
            Lieu (ville, pays)
            <input type="text" name="lieu" maxlength="100" placeholder="Ex: Rome, Italie">
        </label>

        <label>
            Date du voyage
            <input type="date" name="date">
        </label>

        <label>
            Ton histoire
            <textarea name="histoire" rows="6" required placeholder="Raconte ton voyage..."></textarea>
        </label>

        <label>
            Photos (max 5 Mo par photo)
            <input type="file" name="photos[]" multiple accept="image/*">
        </label>

        <button type="submit">Partager mon voyage</button>
    </form>
</div>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/add_trip.js"></script>

</div></div><?php require 'footer.php'; ?>
