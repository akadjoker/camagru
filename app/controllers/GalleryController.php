<?php
class GalleryController {

    public int $currentPage = 1;
    public int $totalPages = 1;

    public function index() {
        global $pdo;
    
        // Número da página (por default é 1)
        $currentPage = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
        $perPage = 5; // Máximo 5 imagens por página
        $offset = ($currentPage - 1) * $perPage;
    
        // Contar número total de imagens
        $stmt = $pdo->query("SELECT COUNT(*) FROM images");
        $totalImages = $stmt->fetchColumn();
        $totalPages = ceil($totalImages / $perPage);
    
        // procura imagens desta página com contagem de likes e comentários
        $stmt = $pdo->prepare("
            SELECT images.*, users.username,
                (SELECT COUNT(*) FROM likes WHERE likes.image_id = images.id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE comments.image_id = images.id) AS comments_count
            FROM images
            JOIN users ON images.user_id = users.id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Likes do utilizador
        $userLikes = [];
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT image_id FROM likes WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $userLikes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    
        // Variáveis para a view
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
    
        include 'views/gallery.php';
    }
    

    public function deleteImage() {
        global $pdo;
    
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }
    
        // para JS usar $_POST (não $_GET)
        $image_id = $_POST['image_id'] ?? null;
    
        if ($image_id) {
            // Verificar se a imagem pertence ao utilizador
            $stmt = $pdo->prepare("SELECT * FROM images WHERE id = :id AND user_id = :user_id");
            $stmt->execute([
                'id' => $image_id,
                'user_id' => $_SESSION['user_id']
            ]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($image) {
                // Apagar ficheiro
                if (file_exists($image['filename'])) {
                    unlink($image['filename']);
                }
    
                // Apagar registo na BD
                $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
                $stmt->execute(['id' => $image_id]);
    
                header("Location: ?page=gallery");
                exit;
            }
        }
    
        // Se não encontrou a imagem ou falhou
        header("Location: ?page=gallery");
        exit;
    }
    
    
    

    public function like() {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }

        $image_id = $_GET['id'] ?? null;

        if ($image_id) {
            // Verificar se já existe o like
            $stmt = $pdo->prepare("SELECT * FROM likes WHERE image_id = :image_id AND user_id = :user_id");
            $stmt->execute([
                'image_id' => $image_id,
                'user_id' => $_SESSION['user_id']
            ]);

            if ($stmt->fetch()) {
                // Já tem like — remover
                $stmt = $pdo->prepare("DELETE FROM likes WHERE image_id = :image_id AND user_id = :user_id");
                $stmt->execute([
                    'image_id' => $image_id,
                    'user_id' => $_SESSION['user_id']
                ]);
            } else {
                // Adicionar like
                $stmt = $pdo->prepare("INSERT INTO likes (image_id, user_id) VALUES (:image_id, :user_id)");
                $stmt->execute([
                    'image_id' => $image_id,
                    'user_id' => $_SESSION['user_id']
                ]);
            }
        }

        header("Location: ?page=gallery");
        exit;
    }


    public function unlike() {
        global $pdo;
    
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }
    
        $image_id = $_GET['id'] ?? null;
    
        if ($image_id) {
            $stmt = $pdo->prepare("DELETE FROM likes WHERE image_id = :image_id AND user_id = :user_id");
            $stmt->execute([
                'image_id' => $image_id,
                'user_id' => $_SESSION['user_id']
            ]);
        }
    
        header("Location: ?page=gallery");
        exit;
    }

    
    public function comment() {
        global $pdo;
    
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }
    
        $image_id = $_POST['image_id'] ?? null;
        $content = trim($_POST['comment'] ?? '');
    
        if ($image_id && !empty($content)) {
            // Gravar comentário
            $stmt = $pdo->prepare("INSERT INTO comments (image_id, user_id, content) VALUES (:image_id, :user_id, :content)");
            $stmt->execute([
                'image_id' => $image_id,
                'user_id' => $_SESSION['user_id'],
                'content' => $content
            ]);
    

            // procurar dono da imagem
            $stmt = $pdo->prepare("
            SELECT users.id, users.email, users.notification_enabled, users.username
            FROM images
            JOIN users ON images.user_id = users.id
            WHERE images.id = :image_id
        ");
        
            $stmt->execute(['image_id' => $image_id]);
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Só envia se não for o próprio autor e tiver notificações ativas
            if ($owner && $owner['notification_enabled'] && $_SESSION['user_id'] != $owner['id']) {
                $to = $owner['email'];
                $subject = "Novo comentário na tua imagem no Camagru";
                $message = "Olá {$owner['username']},\n\nRecebeste um novo comentário na tua imagem.\n\nVisita o Camagru para ver.";
                mail($to, $subject, $message);
            }
        }
    
        $return_to = $_POST['return_to'] ?? 'gallery';
        header("Location: ?page=" . $return_to);
        exit;
    }
    
    public function viewImage() {
        global $pdo;
    
        $id = $_GET['id'] ?? null;
    
        if (!$id) {
            header("Location: ?page=gallery");
            exit;
        }
    
        // Buscar a imagem e o dono
        $stmt = $pdo->prepare("
            SELECT images.*, users.username 
            FROM images 
            JOIN users ON images.user_id = users.id 
            WHERE images.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$image) {
            header("Location: ?page=gallery");
            exit;
        }
    
        // Buscar todos os comentários desta imagem
        $stmt = $pdo->prepare("
            SELECT comments.*, users.username 
            FROM comments 
            JOIN users ON comments.user_id = users.id 
            WHERE comments.image_id = :id 
            ORDER BY comments.created_at ASC
        ");
        $stmt->execute(['id' => $id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        include 'views/viewImage.php';
    }
    public function deleteComment() {
        global $pdo;
    
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }
    
        // Agora deve usar POST
        $comment_id = $_POST['comment_id'] ?? null;
        $image_id = $_POST['image_id'] ?? null;
    
        if ($comment_id && $image_id) {
            // Verificar se o comentário pertence ao utilizador
            $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :id AND user_id = :user_id");
            $stmt->execute([
                'id' => $comment_id,
                'user_id' => $_SESSION['user_id']
            ]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($comment) {
                // Apagar comentário
                $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
                $stmt->execute(['id' => $comment_id]);
            }
        }
    
        header("Location: ?page=image&id=" . $image_id);
        exit;
    }
    
    
}
?>
