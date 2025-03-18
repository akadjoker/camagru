<div class="columns is-centered">
    <div class="column is-half">
        <div class="box">
            <h1 class="title is-4 has-text-centered">Mudar Password</h1>
            
            <?php if (isset($error)): ?>
                <div class="notification is-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="field">
                    <label class="label">Password Atual</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['current_password']) ? 'is-danger' : '' ?>" 
                               type="password" 
                               name="current_password" 
                               placeholder="Password atual">
                        <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['current_password'])): ?>
                        <p class="help is-danger"><?= $errors['current_password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <label class="label">Nova Password</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['new_password']) ? 'is-danger' : '' ?>" 
                               type="password" 
                               name="new_password" 
                               placeholder="Nova password">
                        <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['new_password'])): ?>
                        <p class="help is-danger"><?= $errors['new_password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <label class="label">Confirmar Nova Password</label>
                    <div class="control has-icons-left">
                        <input class="input <?= isset($errors['confirm_password']) ? 'is-danger' : '' ?>" 
                               type="password" 
                               name="confirm_password" 
                               placeholder="Confirmar nova password">
                        <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="help is-danger"><?= $errors['confirm_password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="field">
                    <div class="control">
                        <button class="button is-primary is-fullwidth">Mudar Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>