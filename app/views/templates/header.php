<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Camagru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
/* .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transform: translateY(-5px);
    transition: all 0.3s ease;
    cursor: pointer;
} */
.card-image img {
    transition: transform 0.3s ease;
}

.card:hover .card-image img {
    transform: scale(1.05);
}
</style>

</head>
<body>

<nav class="navbar is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="?page=home">
            <strong>Camagru</strong>
        </a>
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarMenu" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="?page=home">Home</a>
            <a class="navbar-item" href="?page=gallery">Galeria</a>
            <a class="navbar-item" href="?page=editor">Editor</a>
        </div>

        <div class="navbar-end">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="navbar-item">
                    Olá, <?= htmlspecialchars($_SESSION['username']) ?>!
                </div>
                <a class="navbar-item" href="?page=profile">Perfil</a>
                <a class="navbar-item" href="?page=logout">Logout</a>
            <?php else: ?>
                <a class="navbar-item" href="?page=login">Login</a>
                <a class="navbar-item" href="?page=register">Registar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Para o burger funcionar no mobile
    document.addEventListener('DOMContentLoaded', () => {
        const burger = document.querySelector('.navbar-burger');
        const menu = document.getElementById('navbarMenu');
        burger.addEventListener('click', () => {
            burger.classList.toggle('is-active');
            menu.classList.toggle('is-active');
        });
    });
</script>
