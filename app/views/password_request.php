<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Recuperar Password</h1>

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
            <form method="POST" action="?page=password_send">
                <div class="field">
                    <label class="label">O teu Email</label>
                    <input class="input" type="email" name="email" required>
                </div>

                <div class="field">
                    <button class="button is-primary" type="submit">Enviar Email de Recuperação</button>
                </div>
            </form>
        <?php else: ?>
            <div class="has-text-centered">
                <a class="button is-link" href="?page=login">Voltar ao Login</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'views/templates/footer.php'; ?>
