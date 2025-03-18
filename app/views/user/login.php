<div class="columns is-centered">
    <div class="column is-half">
        <div class="box">
            <h1 class="title is-4 has-text-centered">Entrar</h1>
            
            <?php if (isset($error)): ?>
                <div class="notification is-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="field">
                    <label class="label">Nome de Utilizador ou Email</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['username']) ? 'is-danger' : '' ?>" 
                               type="text" 
                               name="username" 
                               placeholder="Nome de utilizador ou email"
                               value="<?= isset($oldInput['username']) ? htmlspecialchars($oldInput['username']) : '' ?>">
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['username'])): ?>
                        <p class="help is-danger"><?= $errors['username'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['password']) ? 'is-danger' : '' ?>" 
                               type="password" 
                               name="password" 
                               placeholder="Password">
                        <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <p class="help is-danger"><?= $errors['password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <div class="control">
                        <button class="button is-primary is-fullwidth">Entrar</button>
                    </div>
                </div>
            </form>
            
            <div class="has-text-centered mt-4">
                <p><a href="/?controller=user&action=forgotPassword">Esqueceste-te da password?</a></p>
                <p class="mt-2">Não tens uma conta? <a href="/?controller=user&action=register">Registar</a></p>
            </div>
        </div>
    </div>
</div>