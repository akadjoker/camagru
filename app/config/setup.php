<?php
require_once 'database.php';

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        confirmed BOOLEAN NOT NULL DEFAULT FALSE,
        notification_enabled BOOLEAN NOT NULL DEFAULT TRUE,
        reset_token VARCHAR(255),
        reset_token_created_at TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS images (
        id SERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        filename VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS comments (
        id SERIAL PRIMARY KEY,
        image_id INTEGER NOT NULL REFERENCES images(id) ON DELETE CASCADE,
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS likes (
        id SERIAL PRIMARY KEY,
        image_id INTEGER NOT NULL REFERENCES images(id) ON DELETE CASCADE,
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(image_id, user_id)
    )"
];

foreach ($tables as $sql) {
    $pdo->exec($sql);
}

echo "Tabelas criadas com sucesso.";
?>
