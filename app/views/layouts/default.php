<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Camagru' ?></title>
    
    <!-- Bulma CSS Framework -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    

    <link rel="stylesheet" href="/css/custom.css">
</head>
<body>
    <!-- Navegação -->
    <nav class="navbar is-dark" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <strong>Camagru</strong>
                </a>
                
                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navMenu">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            
            <div id="navMenu" class="navbar-menu">
                <!-- No navbar-end, substitui o conteúdo existente por: -->
                <div class="navbar-end">
                    <a href="/" class="navbar-item">
                        <span class="icon"><i class="fas fa-home"></i></span>
                        <span>Início</span>
                    </a>
                    
                    <a href="/?controller=gallery" class="navbar-item">
                        <span class="icon"><i class="fas fa-images"></i></span>
                        <span>Galeria</span>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/?controller=editor" class="navbar-item">
                            <span class="icon"><i class="fas fa-camera"></i></span>
                            <span>Editor</span>
                        </a>
                        
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <span class="icon"><i class="fas fa-user"></i></span>
                                <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                            </a>
                            
                            <div class="navbar-dropdown is-right">
                                <a href="/?controller=user&action=profile" class="navbar-item">
                                    <span class="icon"><i class="fas fa-id-card"></i></span>
                                    <span>Perfil</span>
                                </a>
                                <hr class="navbar-divider">
                                <a href="/?controller=user&action=logout" class="navbar-item">
                                    <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                                    <span>Sair</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/?controller=user&action=login" class="navbar-item">
                            <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                            <span>Login</span>
                        </a>
                        <a href="/?controller=user&action=register" class="navbar-item">
                            <span class="icon"><i class="fas fa-user-plus"></i></span>
                            <span>Registro</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Conteúdo principal -->
    <main>
        <section class="section">
            <div class="container">
                <?php include VIEWS . $view . '.php'; ?>
            </div>
        </section>
    </main>
    
    <!-- Rodapé -->
    <footer class="footer">
        <div class="content has-text-centered">
            <p>
                <strong>Camagru</strong> &copy; <?= date('Y') ?>
            </p>
        </div>
    </footer>

    <!-- Modal para mensagens -->

    <div class="modal" id="messageModal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
            <p class="modal-card-title" id="modalTitle">Mensagem</p>
            <button class="delete" aria-label="close" id="closeModalBtn"></button>
                </header>
                <section class="modal-card-body">
                <div id="modalContent"></div>
                </section>
                <footer class="modal-card-foot">
                <button class="button is-primary" id="modalOkBtn">OK</button>
                </footer>
        </div>
    </div>
    
    <!-- Modal de confirmação -->
    <div class="modal" id="confirmModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-warning">
        <p class="modal-card-title" id="confirmTitle">Confirmação</p>
        <button class="delete" aria-label="close" id="closeConfirmBtn"></button>
        </header>
        <section class="modal-card-body">
        <div id="confirmContent"></div>
        </section>
        <footer class="modal-card-foot">
        <button class="button is-danger" id="confirmYesBtn">Sim</button>
        <button class="button" id="confirmNoBtn">Cancelar</button>
        </footer>
    </div>
    </div>
    <script>
        //  menu mobile 
        document.addEventListener('DOMContentLoaded', () => 
    {

        
        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
        if ($navbarBurgers.length > 0) 
        {
            $navbarBurgers.forEach( el => 
            {
                el.addEventListener('click', () => 
                {
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                });
            });
        }
    });

    function showMessage(title, message, type = 'info') 
    {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').innerHTML = message;
            
            const modalHeader = document.querySelector('.modal-card-head');
            modalHeader.className = 'modal-card-head';
            
            if (type === 'error') 
            {
                modalHeader.classList.add('has-background-danger');
                document.getElementById('modalTitle').classList.add('has-text-white');
            } else if (type === 'success') 
            {
                modalHeader.classList.add('has-background-success');
                document.getElementById('modalTitle').classList.add('has-text-white');
            } else 
            {
                modalHeader.classList.add('has-background-info');
                document.getElementById('modalTitle').classList.add('has-text-white');
            }
            
     
            
            document.getElementById('messageModal').classList.add('is-active');
            
      
            
            document.getElementById('closeModalBtn').onclick = function() 
            {
                document.getElementById('messageModal').classList.remove('is-active');
            };
            
       
            
            document.getElementById('modalOkBtn').onclick = function()
             {
                document.getElementById('messageModal').classList.remove('is-active');
            };
        }

 
        // Variável para armazenar a função de callback
        let confirmCallback = null;

 
        function showConfirm(title, message, onConfirm) 
        {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmContent').innerHTML = message;
            
            confirmCallback = onConfirm;
        
            document.getElementById('confirmModal').classList.add('is-active');
        
            document.getElementById('closeConfirmBtn').onclick = function() 
            {
                document.getElementById('confirmModal').classList.remove('is-active');
            };
            
            document.getElementById('confirmYesBtn').onclick = function() 
            {
                document.getElementById('confirmModal').classList.remove('is-active');
                if (confirmCallback) confirmCallback();
            };
            
            document.getElementById('confirmNoBtn').onclick = function() 
            {
                document.getElementById('confirmModal').classList.remove('is-active');
            };
        }

        document.querySelector('#confirmModal .modal-background').onclick = function() 
        {
            document.getElementById('confirmModal').classList.remove('is-active');
        };

    </script>
    <?php if (isset($_SESSION['message_content'])): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            showMessage(
            '<?= htmlspecialchars($_SESSION['message_title'] ?? 'Mensagem') ?>', 
            '<?= htmlspecialchars($_SESSION['message_content']) ?>', 
            '<?= $_SESSION['message_type'] ?? 'info' ?>'
            );
        });
        </script>
        <?php 
        // Limpa as mensagens da sessão após mostrar
        unset($_SESSION['message_title']);
        unset($_SESSION['message_content']);
        unset($_SESSION['message_type']);
        endif; 
        ?>
</body>
</html>
