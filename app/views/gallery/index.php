<!-- app/views/gallery/index.php -->
<div class="container">
    <h1 class="title is-2 has-text-centered">Galeria</h1>
    
    <?php if (empty($images)): ?>
        <div class="notification is-info">
            <p class="has-text-centered">Ainda não existem imagens na galeria.</p>
        </div>
    <?php else: ?>
        <div class="columns is-multiline">
            <?php foreach ($images as $image): ?>
                <div class="column is-one-third">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <a href="/?controller=gallery&action=view&id=<?= $image['id'] ?>">
                                    <img src="<?= $image['filepath'] ?>" alt="Imagem de <?= htmlspecialchars($image['username']) ?>">
                                </a>
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
                                        <div class="level-item">
                                            <span class="icon-text">
                                                <span class="icon has-text-danger">
                                                    <i class="fas <?= $image['is_liked'] ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                                                </span>
                                                <span><?= $image['likes_count'] ?></span>
                                            </span>
                                        </div>
                                        <div class="level-item">
                                            <span class="icon-text">
                                                <span class="icon has-text-info">
                                                    <i class="fas fa-comment"></i>
                                                </span>
                                                <span><?= $image['comments_count'] ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="level-right">
                                        <a href="/?controller=gallery&action=view&id=<?= $image['id'] ?>" class="button is-small">
                                            Ver mais
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
            <nav class="pagination is-centered mt-6" role="navigation" aria-label="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="/?controller=gallery&page=<?= $currentPage - 1 ?>" class="pagination-previous">Anterior</a>
                <?php else: ?>
                    <a class="pagination-previous" disabled>Anterior</a>
                <?php endif; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="/?controller=gallery&page=<?= $currentPage + 1 ?>" class="pagination-next">Seguinte</a>
                <?php else: ?>
                    <a class="pagination-next" disabled>Seguinte</a>
                <?php endif; ?>
                
                <ul class="pagination-list">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li>
                            <a href="/?controller=gallery&page=<?= $i ?>" 
                               class="pagination-link <?= $i === $currentPage ? 'is-current' : '' ?>" 
                               aria-label="Página <?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>