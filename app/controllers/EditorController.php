<?php

class EditorController {

    public function index() {
        global $pdo;

        $recentImages = [];
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT * FROM images WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 6");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $recentImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        include 'views/editor.php';
    }
    

    public function upload_fit() {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }

        // Validação do upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            die("Erro no upload da imagem.");
        }

        $tmpPath = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            die("Formato inválido. Usa JPG ou PNG.");
        }

        // Criar diretório de uploads se não existir
        if (!is_dir('uploads')) {
            mkdir('uploads');
        }

        $newFilename = 'uploads/' . uniqid() . '.png'; // Vamos salvar sempre em PNG

        // --------------------
        // Resize imagem base
        // --------------------

        list($width, $height) = getimagesize($tmpPath);
        $minSize = 64;
        $maxSize = 512;

        $scale = 1;
        if ($width < $minSize || $height < $minSize) {
            $scale = max($minSize / $width, $minSize / $height);
        } elseif ($width > $maxSize || $height > $maxSize) {
            $scale = min($maxSize / $width, $maxSize / $height);
        }

        // Criar imagem base
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $srcImage = imagecreatefromjpeg($tmpPath);
        } else {
            $srcImage = imagecreatefrompng($tmpPath);
        }

        // if ($scale != 1) {
        //     $newWidth = round($width * $scale);
        //     $newHeight = round($height * $scale);

        //     $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        //     imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0,
        //         $newWidth, $newHeight, $width, $height);
        //     imagedestroy($srcImage);
        //     $srcImage = $resizedImage;
        //     $width = $newWidth;
        //     $height = $newHeight;
        // }

        // --------------------
        // Preparar overlay
        // --------------------

        $overlayPath = $_POST['overlay'] ?? '';
        if ($overlayPath && file_exists(ltrim($overlayPath, '/'))) {
            $overlayFullPath = ltrim($overlayPath, '/');
            $overlayImage = imagecreatefrompng($overlayFullPath);

            // Redimensionar overlay
            $overlayWidth = imagesx($overlayImage);
            $overlayHeight = imagesy($overlayImage);
            $newOverlayWidth = intval($_POST['overlay_scale'] ?? 100);
            $scaleFactor = $newOverlayWidth / $overlayWidth;
            $newOverlayHeight = intval($overlayHeight * $scaleFactor);

            $resizedOverlay = imagecreatetruecolor($newOverlayWidth, $newOverlayHeight);
            imagealphablending($resizedOverlay, false);
            imagesavealpha($resizedOverlay, true);
            $transparent = imagecolorallocatealpha($resizedOverlay, 0, 0, 0, 127);
            imagefill($resizedOverlay, 0, 0, $transparent);

            imagecopyresampled($resizedOverlay, $overlayImage, 0, 0, 0, 0,
                $newOverlayWidth, $newOverlayHeight,
                $overlayWidth, $overlayHeight);

            imagedestroy($overlayImage);

            // --------------------
            // Colar overlay na base
            // --------------------

            $posX = intval($_POST['overlay_x'] ?? 0);
            $posY = intval($_POST['overlay_y'] ?? 0);

            imagealphablending($srcImage, true);
            imagesavealpha($srcImage, true);

            imagecopy($srcImage, $resizedOverlay, $posX, $posY, 0, 0,
                $newOverlayWidth, $newOverlayHeight);

            imagedestroy($resizedOverlay);
        }

        // --------------------
        // Aplicar filtro NA IMAGEM FINAL COMPLETA
        // --------------------

        $filter = $_POST['filter'] ?? '';
        switch ($filter) {
            case 'grayscale':
                imagefilter($srcImage, IMG_FILTER_GRAYSCALE);
                break;
            case 'sepia':
                imagefilter($srcImage, IMG_FILTER_GRAYSCALE);
                imagefilter($srcImage, IMG_FILTER_COLORIZE, 90, 60, 40);
                break;
            case 'invert':
                imagefilter($srcImage, IMG_FILTER_NEGATE);
                break;
            case 'brightness':
                imagefilter($srcImage, IMG_FILTER_BRIGHTNESS, 50);
                break;
            // Nenhum filtro = não faz nada
        }

        // --------------------
        // guardae imagem final
        // --------------------

        imagepng($srcImage, $newFilename);
        imagedestroy($srcImage);

        // --------------------
        // Gravar no banco de dados
        // --------------------

        $stmt = $pdo->prepare("INSERT INTO images (user_id, filename) VALUES (:user_id, :filename)");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'filename' => $newFilename
        ]);

        //header("Location: ?page=gallery"); // Redirecionar para galeria
        header("Location: ?page=editor"); // ficmaos no editor ??? fica assim
        exit;
    }

    public function upload() {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            die("Erro no upload da imagem.");
        }

        $tmpPath = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            die("Formato inválido. Usa JPG ou PNG.");
        }

        if (!is_dir('uploads')) {
            mkdir('uploads');
        }

        $newFilename = 'uploads/' . uniqid() . '.png';


        if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
            $errors[] = "Erro no upload da imagem. Tenta novamente.";
            include 'views/editor.php';
            return;
        }
        
        if (!getimagesize($_FILES['image']['tmp_name'])) {
            $errors[] = "Ficheiro inválido ou corrompido.";
            include 'views/editor.php';
            return;
        }
        

        list($width, $height) = getimagesize($tmpPath);

        // Carregar imagem original sem resize
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $srcImage = imagecreatefromjpeg($tmpPath);
        } else {
            $srcImage = imagecreatefrompng($tmpPath);
        }

        // Overlay
        $overlayPath = $_POST['overlay'] ?? '';
        if ($overlayPath && file_exists(ltrim($overlayPath, '/'))) {
            $overlayFullPath = ltrim($overlayPath, '/');
            $overlayImage = imagecreatefrompng($overlayFullPath);

            $overlayWidth = imagesx($overlayImage);
            $overlayHeight = imagesy($overlayImage);
            $newOverlayWidth = intval($_POST['overlay_scale'] ?? 100);
            $scaleFactor = $newOverlayWidth / $overlayWidth;
            $newOverlayHeight = intval($overlayHeight * $scaleFactor);

            $resizedOverlay = imagecreatetruecolor($newOverlayWidth, $newOverlayHeight);
            imagealphablending($resizedOverlay, false);
            imagesavealpha($resizedOverlay, true);
            $transparent = imagecolorallocatealpha($resizedOverlay, 0, 0, 0, 127);
            imagefill($resizedOverlay, 0, 0, $transparent);

            imagecopyresampled($resizedOverlay, $overlayImage, 0, 0, 0, 0,
                $newOverlayWidth, $newOverlayHeight,
                $overlayWidth, $overlayHeight);

            imagedestroy($overlayImage);

            $posX = intval($_POST['overlay_x'] ?? 0);
            $posY = intval($_POST['overlay_y'] ?? 0);

            imagealphablending($srcImage, true);
            imagesavealpha($srcImage, true);

            imagecopy($srcImage, $resizedOverlay, $posX, $posY, 0, 0,
                $newOverlayWidth, $newOverlayHeight);

            imagedestroy($resizedOverlay);
        }

        // Aplicar filtro ao resultado final
        $filter = $_POST['filter'] ?? '';
        switch ($filter) {
            case 'grayscale':
                imagefilter($srcImage, IMG_FILTER_GRAYSCALE);
                break;
            case 'sepia':
                imagefilter($srcImage, IMG_FILTER_GRAYSCALE);
                imagefilter($srcImage, IMG_FILTER_COLORIZE, 90, 60, 40);
                break;
            case 'invert':
                imagefilter($srcImage, IMG_FILTER_NEGATE);
                break;
            case 'brightness':
                imagefilter($srcImage, IMG_FILTER_BRIGHTNESS, 50);
                break;
        }

        imagepng($srcImage, $newFilename);
        imagedestroy($srcImage);

        $stmt = $pdo->prepare("INSERT INTO images (user_id, filename) VALUES (:user_id, :filename)");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'filename' => $newFilename
        ]);

        header("Location: ?page=editor");
        exit;
    }

    public function deleteImage() {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }

        $image_id = $_POST['image_id'] ?? null;

        if ($image_id) {
            $stmt = $pdo->prepare("SELECT * FROM images WHERE id = :id AND user_id = :user_id");
            $stmt->execute([
                'id' => $image_id,
                'user_id' => $_SESSION['user_id']
            ]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($image) {
                if (file_exists($image['filename'])) {
                    unlink($image['filename']);
                }

                $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
                $stmt->execute(['id' => $image_id]);
            }
        }

        header("Location: ?page=editor");
        exit;
    }
}
?>
