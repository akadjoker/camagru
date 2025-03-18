 
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255) NULL,
    reset_token_expiry TIMESTAMP NULL,
    notification_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

 
CREATE TABLE IF NOT EXISTS overlay_images (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE TABLE IF NOT EXISTS user_images (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

 
CREATE TABLE IF NOT EXISTS comments (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    image_id INTEGER NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (image_id) REFERENCES user_images(id) ON DELETE CASCADE
);

 
CREATE TABLE IF NOT EXISTS likes (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    image_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, image_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (image_id) REFERENCES user_images(id) ON DELETE CASCADE
);

 
CREATE INDEX idx_user_images_user_id ON user_images(user_id);
CREATE INDEX idx_comments_image_id ON comments(image_id);
CREATE INDEX idx_comments_user_id ON comments(user_id);
CREATE INDEX idx_likes_image_id ON likes(image_id);
CREATE INDEX idx_likes_user_id ON likes(user_id);
 
INSERT INTO overlay_images (name, filepath) VALUES
('Frame', 'public/overlays/frame.png'),
('Moldura', 'public/overlays/moldura.png'),
('Fire', 'public/overlays/fire.png'),
('Mirror', 'public/overlays/mirror.png'),
('Sunglasses', 'public/overlays/sunglasses.png'),
('Thug Life', 'public/overlays/thug.png'),
('Hat', 'public/overlays/hat.png'),
('Mustache', 'public/overlays/mustache.png'),
('Cat', 'public/overlays/cat.png'),
('Bear', 'public/overlays/bear.png'),
('Witch', 'public/overlays/witch.png');
