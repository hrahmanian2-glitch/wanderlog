<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estConnecte() {
    return isset($_SESSION['email']);
}

function emailActuel() {
    return $_SESSION['email'] ?? null;
}

function prenomActuel() {
    return $_SESSION['prenom'] ?? null;
}

function nomCompletActuel() {
    if (!estConnecte()) return null;
    return ($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanderlog</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo">Wander<span>log</span></a>
    <div class="nav-links">
        <?php if (estConnecte()): ?>
            <a href="profile.php">Bonjour, <?= htmlspecialchars(prenomActuel()) ?></a>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<main>
