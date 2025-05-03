<!-- app/views/gallery/view.php -->
<div class="container">
    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">Início</a></li>
            <li><a href="/?controller=gallery">Galeria</a></li>
            <li class="is-active"><a href="#" aria-current="page">Imagem #<?= $image['id'] ?></a></li>
        </ul>
    </nav>
    
    <div class="columns">
        <div class="column is-two-thirds">
            <div class="card">
                <div class="card-image">
                    <figure class="image">
                        <img src="<?= $image['filepath'] ?>" alt="Imagem de <?= htmlspecialchars($image['username']) ?>">
                    </figure>
                </div>
                <div class="card-content">
                    <div class="media">
                        <div class="media-content">
                            <p class="title is-5"><?= htmlspecialchars($image['username']) ?></p>
                            <p class="subtitle is-6">
                                <?= date('d/m/Y H:i', strtotime($image['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="content">
                        <div class="level">
                            <div class="level-left">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="POST" action="/?controller=gallery&action=like" class="level-item">
                                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                        <button type="submit" class="button is-small <?= $image['is_liked'] ? 'is-danger' : 'is-light' ?>">
                                            <span class="icon <?= $image['is_liked'] ? 'has-text-white' : 'has-text-danger' ?>">
                                                <i class="far <?= $image['is_liked'] ? 'fa-heart' : 'fa-heart' ?>"></i>
                                            </span>
                                            <span><?= $image['likes_count'] ?></span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="level-item">
                                        <span class="icon-text">
                                            <span class="icon has-text-danger">
                                                <i class="far fa-heart"></i>
                                            </span>
                                            <span><?= $image['likes_count'] ?></span>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($image['is_owner']): ?>
                                <div class="level-right">
                                    <!-- <button class="button is-danger is-small" 
                                            onclick="if(confirm('Tens a certeza que queres apagar esta imagem?')) { 
                                                window.location.href='/?controller=editor&action=delete&id=<?= $image['id'] ?>'
                                            }">
                                        <span class="icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        <span>Apagar</span>
                                    </button> -->
                                    <button class="button is-danger is-small"
                                        onclick="showConfirm('Apagar Imagem', 'Tens a certeza que queres apagar esta imagem?', function() {
                                            window.location.href='/?controller=editor&action=delete&id=<?= $image['id'] ?>'
                                        })">
                                        <span class="icon">
                                            <i class="far fa-trash"></i>
                                        </span>
                                        <span>Apagar</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Secção de comentários -->
            <div class="box mt-5">
                <h3 class="title is-4">Comentários (<?= count($image['comments']) ?>)</h3>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="/?controller=gallery&action=comment" class="mb-5">
                        <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                        <div class="field">
                            <div class="control">
                                <textarea class="textarea" name="comment" placeholder="Adiciona um comentário..." required></textarea>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-primary">Comentar</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="notification is-info">
                        <p>Para comentar, é necessário fazer <a href="/?controller=user&action=login">login</a>.</p>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($image['comments'])): ?>
                    <p class="has-text-centered">Ainda não existem comentários.</p>
                <?php else: ?>
                    <?php foreach ($image['comments'] as $comment): ?>
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                                        <small><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></small>
                                        <br>
                                        <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                    </p>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="column">
            <div class="box">
                <h3 class="title is-4">Outras Imagens</h3>
                <!-- Aqui poderíamos mostrar outras imagens do mesmo utilizador ou imagens relacionadas -->
            </div>
        </div>
    </div>
</div>