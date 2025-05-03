<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <?php if ($success): ?>
            <div class="notification is-success">
                Email confirmado com sucesso! Podes agora fazer login.
            </div>
            <div class="has-text-centered">
                <a href="?page=login" class="button is-primary">Ir para o Login</a>
            </div>
        <?php else: ?>
            <div class="notification is-danger">
                Não foi possível confirmar o email. Verifica o link.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'views/templates/footer.php'; ?>
