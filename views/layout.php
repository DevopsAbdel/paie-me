<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Paie Me' ?> — Paie Me</title>
    <link rel="stylesheet" href="/paie-me/assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): 
    $ctx = $_SESSION['societe_context'] ?? null;
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <?php if ($ctx): ?>
            <div style="width:40px;height:40px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;margin-bottom:4px;">
                <?= strtoupper(mb_substr($ctx['raison_sociale'], 0, 2)) ?>
            </div>
            <h2 style="font-size:1rem;margin:0;"><?= htmlspecialchars($ctx['raison_sociale']) ?></h2>
            <small style="font-size:0.7rem;">ICE: <?= htmlspecialchars($ctx['ice']) ?></small>
        <?php else: ?>
            <h2>Paie Me</h2>
            <small>Gestion de paie</small>
        <?php endif; ?>
    </div>
    <ul class="sidebar-nav">
        <?php if ($ctx): ?>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>?tab=infos" class="<?= str_contains($_SERVER['REQUEST_URI'], '/societes/'.$ctx['id']) && !str_contains($_SERVER['REQUEST_URI'], 'tab=') ? 'active' : '' ?>">
                <span class="icon" data-lucide="info"></span>
                <span>Infos société</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>?tab=salaries">
                <span class="icon" data-lucide="users"></span>
                <span>Salariés</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>?tab=paies">
                <span class="icon" data-lucide="wallet"></span>
                <span>Paies</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>?tab=bulletins">
                <span class="icon" data-lucide="file-text"></span>
                <span>Bulletins</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>?tab=cnss">
                <span class="icon" data-lucide="shield-check"></span>
                <span>CNSS / Damancom</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>?tab=ir">
                <span class="icon" data-lucide="calculator"></span>
                <span>IR / SIMPL</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>/parametres" class="<?= str_contains($_SERVER['REQUEST_URI'], '/parametres') ? 'active' : '' ?>">
                <span class="icon" data-lucide="settings"></span>
                <span>Paramètres</span>
            </a>
        </li>
        <?php else: ?>
        <li>
            <a href="/paie-me/dashboard" class="<?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">
                <span class="icon" data-lucide="layout-dashboard"></span>
                <span>Dashboard</span>
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="/paie-me/societes" class="<?= str_contains($_SERVER['REQUEST_URI'], '/societes') && !$ctx ? 'active' : '' ?>">
                <span class="icon" data-lucide="building-2"></span>
                <span><?= $ctx ? 'Changer de société' : 'Sociétés' ?></span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <?php if ($ctx): ?>
        <a href="/paie-me/societes/clear-context">
            <span class="icon" data-lucide="arrow-left-square"></span>
            <span>Quitter la société</span>
        </a>
        <?php endif; ?>
        <a href="/paie-me/logout">
            <span class="icon" data-lucide="log-out"></span>
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

<script>lucide.createIcons();</script>
</body>
</html>
