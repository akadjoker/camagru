<?php
// app/test_frames.php

// Inclui o ficheiro de configuração da base de dados
require_once 'config/database.php';

// Função para obter todos os overlays da base de dados
function getAllOverlays() {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM overlay_images ORDER BY id";
    $stmt = $db->query($query);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtém todos os overlays
$overlays = getAllOverlays();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Frames - Camagru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <style>
        .frame-item {
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .frame-item:hover {
            transform: scale(1.05);
        }
        .frame-image {
            background-color: rgba(200, 200, 200, 0.2);
            border-radius: 4px;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        .frame-image img {
            max-height: 180px;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="title is-2 has-text-centered mb-6">Teste de Frames</h1>
        
        <?php if (empty($overlays)): ?>
            <div class="notification is-warning">
                <p class="has-text-centered">Não foram encontrados frames na base de dados.</p>
            </div>
        <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($overlays as $overlay): ?>
                    <div class="column is-3">
                        <div class="frame-item">
                            <div class="frame-image">
                                <img src="<?= $overlay['filepath'] ?>" alt="<?= htmlspecialchars($overlay['name']) ?>">
                            </div>
                            <div class="has-text-centered mt-2">
                                <p class="is-size-5"><?= htmlspecialchars($overlay['name']) ?></p>
                                <p class="is-size-7 has-text-grey"><?= $overlay['filepath'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>