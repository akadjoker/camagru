<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Perfil do Utilizador</h1>

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

        <!-- Formulário de atualização de username e email -->
        <form method="POST" class="box">
            <h2 class="title is-4">Atualizar Dados</h2>
            <div class="field">
                <label class="label">Username</label>
                <input class="input" type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="field">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="field">
                <button class="button is-primary" type="submit" name="update_profile">Guardar Alterações</button>
            </div>
        </form>

        <!-- Formulário de alteração de password -->
        <form method="POST" class="box mt-5">
            <h2 class="title is-4">Alterar Password</h2>
            <div class="field">
                <label class="label">Nova Password</label>
                <input class="input" type="password" name="password" required>
            </div>
            <div class="field">
                <label class="label">Confirmar Password</label>
                <input class="input" type="password" name="password_confirm" required>
            </div>
            <div class="field">
                <button class="button is-warning" type="submit" name="change_password">Alterar Password</button>
            </div>
        </form>

        <!-- Formulário de notificações -->
        <form method="POST" class="box mt-5">
            <h2 class="title is-4">Notificações</h2>
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="notifications" value="1"
                        <?= $user['notification_enabled'] ? 'checked' : '0' ?>>
                    Quero receber notificações por email.
                </label>
            </div>
            <div class="field">
                <button class="button is-info" type="submit" name="toggle_notifications">Guardar Preferências</button>
            </div>
        </form>

        <div class="has-text-centered mt-5">
            <a href="?page=logout" class="button is-danger">Logout</a>
        </div>
    </div>
</section>

<?php include 'views/templates/footer.php'; ?>
