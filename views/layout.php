<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Paie Me' ?> — Paie Me</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/paie-me/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            <a href="/paie-me/societes/<?= $ctx['id'] ?>/sources-legales" class="<?= str_contains($_SERVER['REQUEST_URI'], '/sources-legales') ? 'active' : '' ?>">
                <span class="icon" data-lucide="book-open"></span>
                <span>Sources légales</span>
            </a>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>/baremes" class="<?= str_contains($_SERVER['REQUEST_URI'], '/baremes') ? 'active' : '' ?>" style="<?= str_contains($_SERVER['REQUEST_URI'], '/baremes') ? 'border-left:3px solid var(--accent);' : '' ?>">
                <span class="icon" data-lucide="ruler"></span>
                <span>Barèmes</span>
            </a>
            <?php if (str_contains($_SERVER['REQUEST_URI'], '/baremes')): ?>
            <ul style="list-style:none; padding:0; margin:0.25rem 0 0 1.5rem; font-size:0.8125rem;">
                <?php $baseB = '/paie-me/societes/' . $ctx['id'] . '/baremes/'; $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
                <li><a href="<?= $baseB ?>anciennete" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/anciennete')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Ancienneté</a></li>
                <li><a href="<?= $baseB ?>conge_annuel" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/conge_annuel')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Congé annuel</a></li>
                <li><a href="<?= $baseB ?>jours_feries" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/jours_feries')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Jours fériés</a></li>
                <li><a href="<?= $baseB ?>impot_revenu" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/impot_revenu')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Impôt sur le revenu</a></li>
                <li><a href="<?= $baseB ?>heures_sup" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/heures_sup')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Heures sup</a></li>
            </ul>
            <?php endif; ?>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>/reglages" class="<?= str_contains($_SERVER['REQUEST_URI'], '/reglages') ? 'active' : '' ?>" style="<?= str_contains($_SERVER['REQUEST_URI'], '/reglages') ? 'border-left:3px solid var(--accent);' : '' ?>">
                <span class="icon" data-lucide="sliders"></span>
                <span>Réglages</span>
            </a>
            <?php if (str_contains($_SERVER['REQUEST_URI'], '/reglages')): ?>
            <ul style="list-style:none; padding:0; margin:0.25rem 0 0 1.5rem; font-size:0.8125rem;">
                <?php $baseR = '/paie-me/societes/' . $ctx['id'] . '/reglages/'; $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
                <li><a href="<?= $baseR ?>cnss_amo" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/cnss_amo')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">CNSS et AMO</a></li>
                <li><a href="<?= $baseR ?>organismes_sociaux" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/organismes_sociaux')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Organismes Sociaux</a></li>
            </ul>
            <?php endif; ?>
        </li>
        <li>
            <a href="/paie-me/societes/<?= $ctx['id'] ?>/parametres" class="<?= str_contains($_SERVER['REQUEST_URI'], '/parametres') ? 'active' : '' ?>" style="<?= str_contains($_SERVER['REQUEST_URI'], '/parametres') ? 'border-left:3px solid var(--accent);' : '' ?>">
                <span class="icon" data-lucide="settings"></span>
                <span>Paramètres</span>
            </a>
            <?php if (str_contains($_SERVER['REQUEST_URI'], '/parametres')): ?>
            <ul style="list-style:none; padding:0; margin:0.25rem 0 0 1.5rem; font-size:0.8125rem;">
                <?php $baseP = '/paie-me/societes/' . $ctx['id'] . '/parametres/'; $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $tab = basename($uri); if ($tab === 'parametres') $tab = 'banque'; ?>
                <li><a href="<?= $baseP ?>general" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/parametres/general')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Général</a></li>
                <li><a href="<?= $baseP ?>banque" style="display:block; padding:0.3rem 0.5rem; color:<?= $tab==='banque'?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Banque</a></li>
                <li><a href="<?= $baseP ?>teleservices" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/teleservices')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Téléservices</a></li>
                <li><a href="<?= $baseP ?>bcp" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/bcp')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">BCP</a></li>
                <li><a href="<?= $baseP ?>services" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/services')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Services</a></li>
                <li><a href="<?= $baseP ?>gains" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/gains')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Gains</a></li>
                <li><a href="<?= $baseP ?>retenues" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/retenues')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Retenues</a></li>
                <li><a href="<?= $baseP ?>attestations" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/attestations')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Attestations</a></li>
                <li><a href="<?= $baseP ?>codification" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/codification')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Codification</a></li>
                <li><a href="<?= $baseP ?>journal" style="display:block; padding:0.3rem 0.5rem; color:<?= str_contains($uri, '/journal')?'var(--accent)':'var(--text-muted)'?>; text-decoration:none; border-radius:4px;">Journal comptable</a></li>
            </ul>
            <?php endif; ?>
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

<div class="toast-container" id="toastContainer"></div>

<script>lucide.createIcons();</script>
<?php if (isset($pageScripts)): foreach ((array)$pageScripts as $s): ?>
<script src="<?= htmlspecialchars($s) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
