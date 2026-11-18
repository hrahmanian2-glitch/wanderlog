<?php
require 'data.php';
require 'header.php';

$erreur = '';
$message = '';

if (isset($_GET['msg']) && $_GET['msg'] === 'inscription_ok') {
    $message = 'Inscription réussie ! Tu peux maintenant te connecter.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mdp'] ?? '';

    if ($email === '' || $mdp === '') {
        $erreur = 'Email et mot de passe obligatoires.';
    } else if (connexionOk($email, $mdp)) {
        $user = getUser($email);
        $_SESSION['email']  = $user['email'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['nom']    = $user['nom'];

        header('Location: index.php');
        exit;
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
?>

<div class="auth-layout">

    <div class="auth-panel">
        <div class="auth-brand">Wanderlog</div>
        <h2 class="auth-tagline">Tes voyages racontent<br><em>ton histoire.</em></h2>
        <p class="auth-desc">
            Chaque voyage est un récit. Ensemble, ils forment
            l'histoire de ta vie — géolocalisée, partagée, vivante.
        </p>
        <ul class="auth-features">
            <li>Épingle tes voyages sur la carte du monde</li>
            <li>Raconte ton histoire en photos et en mots</li>
            <li>Découvre les récits des autres en temps réel</li>
        </ul>
    </div>

    <div class="auth-form-wrap">
        <div class="form-card">
            <h1>Connexion</h1>
            <p class="subtitle">Bienvenue, connecte-toi pour accéder à ta carte.</p>

            <?php if ($message !== ''): ?>
                <div class="success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($erreur !== ''): ?>
                <div class="error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <label>
                    Email
                    <input type="email" name="email" required placeholder="ton@email.com">
                </label>

                <label>
                    Mot de passe
                    <input type="password" name="mdp" required placeholder="••••••••">
                </label>

                <button type="submit">Se connecter</button>
            </form>

            <p class="form-link">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
        </div>
    </div>

</div>

<?php require 'footer.php'; ?>
