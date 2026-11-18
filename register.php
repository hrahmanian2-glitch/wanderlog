<?php
require 'data.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $mdp    = $_POST['mdp']  ?? '';
    $mdp2   = $_POST['mdp2'] ?? '';

    if ($prenom === '' || $nom === '' || $email === '' || $mdp === '') {
        $erreur = 'Tous les champs sont obligatoires.';
    } else if (strpos($prenom, ',') !== false || strpos($nom, ',') !== false || strpos($email, ',') !== false) {
        $erreur = 'Les virgules ne sont pas autorisées.';
    } else if (strlen($mdp) < 6) {
        $erreur = 'Le mot de passe doit faire au moins 6 caractères.';
    } else if ($mdp !== $mdp2) {
        $erreur = 'Les deux mots de passe ne correspondent pas.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Email invalide.';
    } else if (emailExiste($email)) {
        $erreur = 'Cet email est déjà utilisé.';
    } else {
        ajouterUser($prenom, $nom, $email, $mdp);
        header('Location: login.php?msg=inscription_ok');
        exit;
    }
}

require 'header.php';
?>

<div class="container">
    <div class="form-card">
        <h1>Inscription</h1>
        <p class="subtitle">Crée ton compte et commence à partager tes voyages.</p>

        <?php if ($erreur !== ''): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <label>
                Prénom
                <input type="text" name="prenom" required placeholder="Marie">
            </label>

            <label>
                Nom
                <input type="text" name="nom" required placeholder="Dupont">
            </label>

            <label>
                Email
                <input type="email" name="email" required placeholder="marie@exemple.com">
            </label>

            <label>
                Mot de passe
                <input type="password" name="mdp" required minlength="6" placeholder="6 caractères minimum">
            </label>

            <label>
                Confirmer le mot de passe
                <input type="password" name="mdp2" required minlength="6" placeholder="••••••••">
            </label>

            <button type="submit">Créer mon compte</button>
        </form>

        <p class="form-link">Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>
</div>

<?php require 'footer.php'; ?>
