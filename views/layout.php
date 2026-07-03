<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Paie Me' ?> — Paie Me</title>
    <link rel="stylesheet" href="/paie-me/assets/css/style.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>Paie Me</h2>
        <small>Gestion de paie</small>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="/paie-me/dashboard" class="<?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes" class="<?= str_contains($_SERVER['REQUEST_URI'], '/societes') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>Sociétés</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/salaries" class="<?= str_contains($_SERVER['REQUEST_URI'], '/salaries') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>Salariés</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/paies" class="<?= str_contains($_SERVER['REQUEST_URI'], '/paies') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>Paies</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/bulletins" class="<?= str_contains($_SERVER['REQUEST_URI'], '/bulletins') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>Bulletins</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/damancom" class="<?= str_contains($_SERVER['REQUEST_URI'], '/damancom') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>CNSS / Damancom</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/ir" class="<?= str_contains($_SERVER['REQUEST_URI'], '/ir') ? 'active' : '' ?>">
                <span class="icon">&#9632;</span>
                <span>IR / SIMPL</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="/paie-me/logout">
            <span class="icon">&#9632;</span>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>
<?php endif; ?>

<main class="<?= isset($_SESSION['user_id']) ? 'main-content' : '' ?>">
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="topbar">
        <h1><?= $title ?? 'Paie Me' ?></h1>
        <div class="topbar-actions">
            <?= $actions ?? '' ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $flash = \Core\Session::getFlash('success');
    if ($flash): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    <?php $flash = \Core\Session::getFlash('error');
    if ($flash): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    <?php $flash = \Core\Session::getFlash('warning');
    if ($flash): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?= $content ?? '' ?>
</main>

</body>
</html>
