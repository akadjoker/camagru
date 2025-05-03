<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Redefinir Password</h1>

        <?php if (!empty($errors)): ?>
            <div class="notification is-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="notification is-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($success)): ?>
            <form method="POST" action="?page=update_password">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="field">
                    <label class="label">Nova Password</label>
                    <input class="input" type="password" name="password" required>
                </div>

                <div class="field">
                    <label class="label">Confirmar Nova Password</label>
                    <input class="input" type="password" name="password_confirm" required>
                </div>

                <div class="field">
                    <button class="button is-primary" type="submit" name="update_password">Redefinir Password</button>
                </div>
            </form>

        <?php else: ?>
            <div class="has-text-centered">
                <a class="button is-link" href="?page=login">Ir para o Login</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'views/templates/footer.php'; ?>
