<div class="columns is-centered">
    <div class="column is-half">
        <div class="box">
            <div class="has-text-centered">
                <span class="icon is-large has-text-success">
                    <i class="fas fa-check-circle fa-3x"></i>
                </span>
                
                <h1 class="title is-4 mt-4">Registo Concluído!</h1>
                
                <p class="mb-4">
                    Enviámos um email de confirmação para o teu endereço de email.
                    Por favor, verifica a tua caixa de entrada e clica no link de confirmação para ativar a tua conta.
                </p>
                
                <p class="is-size-7 has-text-grey">
                    Se não receberes o email dentro de alguns minutos, verifica a pasta de spam.
                </p>
                
                <div class="mt-5">
                    <a href="/?controller=user&action=login" class="button is-link">
                        Ir para a página de login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

 
<?php if (isset($_SESSION['debug_token'])): ?>
    <div class="notification is-warning mt-4">
        <p><strong>Debug (remover em produção):</strong></p>
        <p>Token de verificação: <?= $_SESSION['debug_token'] ?></p>
    </div>
    <?php unset($_SESSION['debug_token']); ?>
<?php endif; ?>