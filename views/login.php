<div class="login-wrapper">
    <div class="login-card" style="text-align:center;">
        <i data-lucide="wallet" style="width:40px;height:40px;stroke:var(--accent);stroke-width:1.5;margin-bottom:0.5rem;"></i>
        <h1>Paie Me</h1>
        <p class="subtitle">Gestion de paie — Maroc</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="text-align:left;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/paie-me/login" style="max-width:320px; margin:0 auto;">
            <div class="form-group" style="text-align:left;">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="admin@paie-me.ma" required>
            </div>
            <div class="form-group" style="text-align:left;">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" value="admin123" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>

        <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--border);">
            <p class="subtitle" style="margin-bottom:0.75rem;">Pas de compte ? Essayez l'application avec des données de démonstration :</p>
            <a href="/paie-me/demo" class="btn btn-info" style="width:100%;">
                <i data-lucide="flask-conical" style="width:16px;height:16px;vertical-align:-2px;margin-right:0.4rem;"></i>
                Entrer en mode démo
            </a>
        </div>
    </div>
</div>
