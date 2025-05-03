<div class="columns is-centered">
    <div class="column is-half">
        <div class="box">
            <h1 class="title is-4 has-text-centered">Recuperar Password</h1>
            
            <?php if (isset($error)): ?>
                <div class="notification is-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <p class="mb-4 has-text-centered">
                Introduz o teu email e enviaremos um link para redefinires a tua password.
            </p>
            
            <form method="POST">
                <div class="field">
                    <label class="label">Email</label>
                    <div class="control has-icons-left">
                        <input class="input" 
                               type="email" 
                               name="email" 
                               placeholder="Email"
                               value="<?= isset($oldInput['email']) ? htmlspecialchars($oldInput['email']) : '' ?>">
                        <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
                
                <div class="field">
                    <div class="control">
                        <button class="button is-primary is-fullwidth">Enviar Link de Recuperação</button>
                    </div>
                </div>
            </form>
            
            <div class="has-text-centered mt-4">
                <a href="/?controller=user&action=login">Voltar ao Login</a>
            </div>
        </div>
    </div>
</div>