<div class="login-wrapper">
    <div class="login-card">
        <div style="font-size:2.5rem; margin-bottom:0.5rem;">&#9632;</div>
        <h1>Paie Me</h1>
        <p class="subtitle">Gestion de paie — Maroc</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/paie-me/login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="admin@paie-me.ma" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" value="admin123" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
    </div>
</div>
