<!-- File : app/views/layouts/auth.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Plateforme Mémoires</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
</head>
<body class="auth-body">

    <div class="auth-container">

        <div class="auth-card">

            <div class="auth-logo">
                <span class="logo-circle">M</span>
                <h1>Plateforme Mémoires</h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/login">

                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="votre@email.bj"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    SE CONNECTER →
                </button>

            </form>

            <p class="auth-note">
                Les comptes sont créés par la Direction des Études
            </p>

        </div>

    </div>

</body>
</html>