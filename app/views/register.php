<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Registar</h1>

        <?php if (!empty($this->errors)): ?>
            <div class="notification is-danger">
                <ul>
                    <?php foreach ($this->errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=register" class="box">
            <div class="field">
                <label class="label">Username</label>
                <div class="control">
                    <input class="input" type="text" name="username" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Email</label>
                <div class="control">
                    <input class="input" type="email" name="email" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Password</label>
                <div class="control">
                    <input class="input" type="password" name="password" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Confirmar Password</label>
                <div class="control">
                    <input class="input" type="password" name="password_confirm" required>
                </div>
            </div>

            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <button class="button is-primary" type="submit">Criar Conta</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include 'views/templates/footer.php'; ?>
