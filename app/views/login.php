<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Login</h1>

        <?php if (!empty($this->errors)): ?>
            <div class="notification is-danger">
                <ul>
                    <?php foreach ($this->errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['message']) && $_GET['message'] == 'password_reset_success'): ?>
            <div class="notification is-success">
                Password alterada com sucesso. Podes agora iniciar sessão.
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=login" class="box">
            <div class="field">
                <label class="label">Username</label>
                <div class="control">
                    <input class="input" type="text" name="username" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Password</label>
                <div class="control">
                    <input class="input" type="password" name="password" required>
                </div>
            </div>

            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <button class="button is-primary" type="submit">Entrar</button>
                </div>
            </div>
            <p class="has-text-left mt-3">
                <a href="?page=password_request">Esqueceste a password?</a>
            </p>

        </form>
    

    </div>
</section>

<?php include 'views/templates/footer.php'; ?>
