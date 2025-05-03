<?php include 'views/templates/header.php'; ?>

<div class="columns is-centered">
    <div class="column is-8">
        <div class="has-text-centered mb-6">
            <h1 class="title is-2">Bem-vindo ao Camagru</h1>
            <p class="subtitle is-4">Cria, edita e partilha fotos incríveis</p>
            <div class="hero-body has-text-centered">
            <div class="buttons is-centered">

    </div>
    </div>
        </div>



        <div class="columns is-multiline">
            <div class="column is-4">
                <div class="card">
                    <div class="card-content has-text-centered">
                        <span class="icon is-large">
                            <i class="fas fa-camera fa-3x"></i>
                        </span>
                        <p class="title is-4 mt-4">Captura</p>
                        <p class="subtitle is-6">Usa tua webcam ou faz upload de imagens</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-4">
                <div class="card">
                    <div class="card-content has-text-centered">
                        <span class="icon is-large">
                            <i class="fas fa-edit fa-3x"></i>
                        </span>
                        <p class="title is-4 mt-4">Edita</p>
                        <p class="subtitle is-6">Adiciona molduras e sobreposições divertidas</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-4">
                <div class="card">
                    <div class="card-content has-text-centered">
                        <span class="icon is-large">
                            <i class="fas fa-share-alt fa-3x"></i>
                        </span>
                        <p class="title is-4 mt-4">Compartilha</p>
                        <p class="subtitle is-6">Publica e interage com os teus amigos</p>
                    </div>
                </div>
            </div>
        </div>
        <section class="section">
  <div class="container">
    <h2 class="title is-3 has-text-centered">Descobre o que podes fazer com o Camagru</h2>
    <div class="columns">
      <div class="column">
        <div class="card">
          <div class="card-image">
            <figure class="image is-4by3">
              <img src="assets/images/banner1.png" alt="Cria, edita e partilha!">
            </figure>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="card">
          <div class="card-image">
            <figure class="image is-4by3">
              <img src="assets/images/banner2.png" alt="Webcam, filtros e sobreposições fáceis!">
            </figure>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="card">
          <div class="card-image">
            <figure class="image is-4by3">
              <img src="assets/images/banner3.png" alt="Partilha momentos únicos">
            </figure>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

        <div class="mt-6">
            <h2 class="title is-3 has-text-centered mb-5">Recentes da Galeria</h2>
            
            <div class="columns is-multiline">
                <?php if (empty($recentImages)): ?>
                    <div class="column is-12">
                        <p class="has-text-centered">Ainda não existem imagens na galeria.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentImages as $image): ?>
                        <div class="column is-4">
                            <div class="card">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <a href="?page=image&id=<?= $image['id'] ?>">
                                            <img src="<?= htmlspecialchars($image['filepath']) ?>" alt="Imagem de <?= htmlspecialchars($image['username']) ?>">
                                        </a>
                                    </figure>
                                </div>
                                <div class="card-content">
                                    <div class="content">
                                        <p>Uma criação de <?= htmlspecialchars($image['username']) ?></p>
                                        <div class="is-flex is-justify-content-space-between">
                                            <span><i class="far fa-heart"></i> <?= $image['likes_count'] ?? 0 ?></span>
                                            <span><i class="far fa-comment"></i> <?= $image['comments_count'] ?? 0 ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="has-text-centered mt-4">
                <a href="?page=gallery" class="button is-link">
                    Ver mais na galeria
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
