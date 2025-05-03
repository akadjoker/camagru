<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title">Imagem de <?= htmlspecialchars($image['username']) ?></h1>

        <figure class="image">
            <img src="<?= htmlspecialchars($image['filename']) ?>" style="max-width: 500px; height:auto;" alt="Imagem">
        </figure>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $image['user_id']): ?>
            <button class="button is-small is-danger mt-2"
                onclick="openGenericModal(
                    'Tens a certeza que queres apagar esta imagem?',
                    '?page=delete_image',
                    {'image_id': <?= $image['id'] ?>}
                )"
                title="Apagar imagem">
                <span class="icon"><i class="fas fa-trash-alt"></i></span>
            </button>
        <?php endif; ?>

        <hr>

        <h2 class="subtitle">Comentários:</h2>

        <?php if (empty($comments)): ?>
            <p>Esta imagem ainda não tem comentários.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="box is-flex is-justify-content-space-between">
                    <div>
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['content']) ?></p>
                    </div>
                    <div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                        <button class="button is-small is-danger"
                            onclick="openGenericModal(
                                'Queres mesmo apagar este comentário?',
                                '?page=delete_comment',
                                {'comment_id': <?= $comment['id'] ?>, 'image_id': <?= $image['id'] ?>}
                            )"
                            title="Apagar comentário">
                            <span class="icon"><i class="fas fa-trash-alt"></i></span>
                        </button>
                    <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="?page=comment" class="mt-4">
                <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                <input type="hidden" name="return_to" value="image&id=<?= $image['id'] ?>">

                <div class="field has-addons">
                    <div class="control is-expanded">
                        <textarea class="textarea" name="comment" placeholder="Escreve um comentário..." required maxlength="512"></textarea>
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit" title="Adicionar comentário">
                            <span class="icon"><i class="fas fa-comment-dots"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <p><a href="?page=login">Inicia sessão para comentar.</a></p>
        <?php endif; ?>
    </div>
</section>

<!-- Modal genérico -->
<div class="modal" id="genericModal">
  <div class="modal-background"></div>
  <div class="modal-content">
    <div class="box has-text-centered">
      <p id="genericModalMessage" class="title is-5">Tens a certeza?</p>
      <form method="POST" id="genericModalForm">
        <!-- Campos escondidos serão adicionados via JS -->
        <button type="submit" class="button is-danger">Confirmar</button>
        <button type="button" class="button is-light" onclick="closeGenericModal()">Cancelar</button>
      </form>
    </div>
  </div>
  <button class="modal-close is-large" aria-label="close" onclick="closeGenericModal()"></button>
</div>

<script>
function openGenericModal(message, formAction, hiddenFields) {
    document.getElementById('genericModalMessage').textContent = message;
    const form = document.getElementById('genericModalForm');
    form.action = formAction;
    form.querySelectorAll('input[type=hidden]').forEach(e => e.remove());
    for (const [name, value] of Object.entries(hiddenFields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }
    document.getElementById('genericModal').classList.add('is-active');
}

function closeGenericModal() {
    document.getElementById('genericModal').classList.remove('is-active');
}
</script>

<?php include 'views/templates/footer.php'; ?>
