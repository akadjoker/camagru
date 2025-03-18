 
<div class="columns is-centered">
    <div class="column is-half">
        <div class="box">
            <h1 class="title is-4 has-text-centered">Perfil</h1>
            
            <?php if (isset($error)): ?>
                <div class="notification is-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="notification is-success">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="field">
                    <label class="label">Nome de Utilizador</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['username']) ? 'is-danger' : '' ?>" 
                               type="text" 
                               name="username" 
                               placeholder="Nome de utilizador"
                               value="<?= htmlspecialchars($user->username) ?>">
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['username'])): ?>
                        <p class="help is-danger"><?= $errors['username'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <label class="label">Email</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['email']) ? 'is-danger' : '' ?>" 
                               type="email" 
                               name="email" 
                               placeholder="Email"
                               value="<?= htmlspecialchars($user->email) ?>">
                        <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <p class="help is-danger"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" 
                                   name="notification_enabled" 
                                   <?= $user->notification_enabled ? 'checked' : '' ?>>
                            Receber notificações por email quando as minhas imagens receberem comentários
                        </label>
                    </div>
                </div>
                
                <div class="field">
                    <div class="control">
                        <button class="button is-primary is-fullwidth">Guardar Alterações</button>
                    </div>
                </div>
            </form>
            
            <div class="has-text-centered mt-5">
                <div class="buttons is-centered">
                    <a href="/?controller=user&action=changePassword" class="button is-link">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <span>Mudar Password</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>