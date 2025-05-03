<?php include 'views/templates/header.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Galeria</h1>

        <div class="columns is-multiline">
            <?php if (empty($images)): ?>
                <p class="has-text-centered">Ainda não existem imagens na galeria.</p>
            <?php else: ?>
                <?php foreach ($images as $img): ?>
                    <div class="column is-4">
                        <div class="card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <a href="?page=image&id=<?= $img['id'] ?>">
                                        <img src="<?= htmlspecialchars($img['filename']) ?>" alt="Imagem">
                                    </a>
                                </figure>
                            </div>
                            <div class="card-content">
                                <p><strong>Autor:</strong> <?= htmlspecialchars($img['username']) ?></p>
                                <p><strong>Gostos:</strong> <?= $img['likes_count'] ?> | <strong>Comentários:</strong> <?= $img['comments_count'] ?></p>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <div class="action-buttons">

                                        <!-- Like / Unlike -->
                                        <?php if (in_array($img['id'], $userLikes)): ?>
                                            <a class="button is-small is-danger"
                                               href="?page=unlike&id=<?= $img['id'] ?>"
                                               title="Remover gosto">
                                                <span class="icon"><i class="fas fa-heart-broken"></i></span>
                                            </a>
                                        <?php else: ?>
                                            <a class="button is-small is-primary"
                                               href="?page=like&id=<?= $img['id'] ?>"
                                               title="Gostar">
                                                <span class="icon"><i class="fas fa-heart"></i></span>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Apagar imagem (se for dono) -->
                                        <?php if ($_SESSION['user_id'] == $img['user_id']): ?>
                                            <button class="button is-danger is-small"
                                onclick="openGenericModal(
                                    'Tens a certeza que queres apagar esta imagem?',
                                    '?page=delete_image',
                                    {'image_id': <?= $img['id'] ?>}
                                )">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Formulário de comentário -->
                                    <form method="POST" action="?page=comment" class="mt-3">
                                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                        <input type="hidden" name="return_to" value="gallery">
                                        <div class="field has-addons">
                                            <div class="control is-expanded">
                                                <input class="input" name="comment" type="text"
                                                       placeholder="Escreve um comentário..." required>
                                            </div>
                                            <div class="control">
                                                <button class="button is-link is-small" type="submit" title="Adicionar comentário">
                                                    <span class="icon"><i class="fas fa-comment-dots"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                <?php else: ?>
                                    <p><a href="?page=login">Inicia sessão para gostar ou comentar.</a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .action-buttons a, .action-buttons button {
        margin-right: 5px;
    }
</style>

<?php if ($totalPages > 1): ?>
<nav class="pagination is-centered" role="navigation" aria-label="pagination">
    <?php if ($currentPage > 1): ?>
        <a class="pagination-previous" href="?page=gallery&page_num=<?= $currentPage - 1 ?>">Anterior</a>
    <?php else: ?>
        <a class="pagination-previous" disabled>Anterior</a>
    <?php endif; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a class="pagination-next" href="?page=gallery&page_num=<?= $currentPage + 1 ?>">Próxima</a>
    <?php else: ?>
        <a class="pagination-next" disabled>Próxima</a>
    <?php endif; ?>

    <ul class="pagination-list">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li>
                <a class="pagination-link <?= ($p == $currentPage) ? 'is-current' : '' ?>"
                   href="?page=gallery&page_num=<?= $p ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<script>
 

function openGenericModal(message, formAction, hiddenFields) {
    document.getElementById('genericModalMessage').textContent = message;
    const form = document.getElementById('genericModalForm');
    form.action = formAction;

    // Limpa inputs antigos
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


<div class="modal" id="genericModal">
  <div class="modal-background"></div>
  <div class="modal-content">
    <div class="box has-text-centered">
      <p id="genericModalMessage" class="title is-5">Tens a certeza que queres apagar esta imagem?</p>
      <form method="POST" id="genericModalForm">
        <!-- Campos escondidos serão adicionados via JS -->
        <button type="submit" class="button is-danger">Confirmar</button>
        <button type="button" class="button is-light" onclick="closeGenericModal()">Cancelar</button>
      </form>
    </div>
  </div>
  <button class="modal-close is-large" aria-label="close" onclick="closeGenericModal()"></button>
</div>
<?php include 'views/templates/footer.php'; ?>
