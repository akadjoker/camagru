<?php


class EditorController extends BaseController 
{
    private $overlayModel;
    private $imageModel;
    
    public function __construct() 
    {
        $this->overlayModel = new Overlay();
        $this->imageModel = new Image();
    }
    
  
    public function index() 
    {
   
        if (!isset($_SESSION['user_id'])) 
        {
            $this->redirect('/?controller=user&action=login');
            return;
        }
        
    
        $overlays = $this->overlayModel->getAllOverlays();
        
   
        $userImages = $this->imageModel->getUserImages($_SESSION['user_id']);
        
   
        $this->render('editor/index', [
            'pageTitle' => 'Editor de Imagens',
            'overlays' => $overlays,
            'userImages' => $userImages
        ]);
    }


private function applyFilter($imagePath, $filter) 
{
   
    
    if (!file_exists($imagePath))
     {
        return false;
    }
    

    
    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
    if ($extension === 'png') 
    {
        $image = imagecreatefrompng($imagePath);
    } elseif ($extension === 'jpg' || $extension === 'jpeg') 
    {
        $image = imagecreatefromjpeg($imagePath);
    } elseif ($extension === 'gif') 
    {
        $image = imagecreatefromgif($imagePath);
    } else 
    {
        return false;
    }
    

    
    switch ($filter) 
    {
        case 'grayscale':
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            break;
        case 'sepia':
      
            
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            imagefilter($image, IMG_FILTER_COLORIZE, 90, 60, 30);
            break;
        case 'invert':
            imagefilter($image, IMG_FILTER_NEGATE);
            break;
        case 'brightness':
            imagefilter($image, IMG_FILTER_BRIGHTNESS, 30);
            break;
        case 'contrast':
            imagefilter($image, IMG_FILTER_CONTRAST, -10);
            break;
    }
    

    
    if ($extension === 'png')
     {
        imagepng($image, $imagePath);
    } elseif ($extension === 'jpg' || $extension === 'jpeg') 
    {
        imagejpeg($image, $imagePath);
    } elseif ($extension === 'gif') 
    {
        imagegif($image, $imagePath);
    }
    

    imagedestroy($image);
    
    return true;
}

// public function upload() 
// {

//     if (!isset($_SESSION['user_id'])) 
//     {
//         header('Content-Type: application/json');
//         echo json_encode(['error' => 'Não autorizado']);
//         exit;
//     }
    

//     $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';
    

//     if (!file_exists($uploadDir)) 
//     {
//         mkdir($uploadDir, 0777, true);
//     }
    

//     if (isset($_POST['webcam_image'])) 
//     {
//         // Upload via webcam (base64)
//         $img = $_POST['webcam_image'];
//         $img = str_replace('data:image/png;base64,', '', $img);
//         $img = str_replace(' ', '+', $img);
//         $data = base64_decode($img);
        
//         // Gera um nome único para o ficheiro
//         $filename = uniqid() . '.png';
//         $filepath = '/public/uploads/' . $filename;
//         $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filepath;
        
//         // Guarda a imagem
//         file_put_contents($fullPath, $data);
        
//         if (isset($_POST['overlay_id']) && !empty($_POST['overlay_id'])) 
//         {
//             $overlay_id = (int)$_POST['overlay_id'];
//             if ($this->overlayModel->getOverlayById($overlay_id)) 
//             {
//                 $this->applyOverlay($fullPath, $_SERVER['DOCUMENT_ROOT'] . $this->overlayModel->filepath);
//             }
//         }

 
//         if (isset($_POST['filter']) && !empty($_POST['filter'])) 
//         {
//             $filter = $_POST['filter'];
//             $this->applyFilter($fullPath, $filter);
//         }

//     } 
//     elseif (isset($_FILES['file_image'])) 
//     {
   
//         $file = $_FILES['file_image'];
        
//         // Verifica o tipo de ficheiro
//         $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
//         if (!in_array($file['type'], $allowedTypes)) 
//         {
//             header('Content-Type: application/json');
//             echo json_encode(['error' => 'Tipo de ficheiro não permitido']);
//             exit;
//         }
        
//         // Gera um nome único para o ficheiro
//         $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
//         $filename = uniqid() . '.' . $extension;
//         $filepath = '/public/uploads/' . $filename;
//         $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filepath;
        
//         // Move o ficheiro carregado
//         move_uploaded_file($file['tmp_name'], $fullPath);
        
//         // Aplica a sobreposição se fornecida
//         if (isset($_POST['overlay_id']) && !empty($_POST['overlay_id'])) 
//         {
//             $overlay_id = (int)$_POST['overlay_id'];
//             if ($this->overlayModel->getOverlayById($overlay_id)) 
//             {
//                 $this->applyOverlay($fullPath, $_SERVER['DOCUMENT_ROOT'] . $this->overlayModel->filepath);
//             }
//         }
//         if (isset($_POST['filter']) && !empty($_POST['filter'])) 
//         {
//             $filter = $_POST['filter'];
//             $this->applyFilter($fullPath, $filter);
//         }

//     } 
//     else 
//     {
//         header('Content-Type: application/json');
//         echo json_encode(['error' => 'Nenhuma imagem fornecida']);
//         exit;
//     }
    
//     // Guarda a imagem na base de dados
//     if ($this->imageModel->saveImage($_SESSION['user_id'], $filepath)) 
//     {
//         header('Content-Type: application/json');
//         echo json_encode([
//             'success' => true,
//             'image_id' => $this->imageModel->id,
//             'filepath' => $filepath
//         ]);
//     } 
//     else 
//     {
//         header('Content-Type: application/json');
//         echo json_encode(['error' => 'Erro ao guardar a imagem']);
//     }
    
//     exit;
// }
    
public function upload() 
{

    error_reporting(E_ERROR); // Reportar apenas erros fatais
    ini_set('display_errors', 0); // Não exibir erros
    ob_clean(); // Limpar buffer de saída

     if (!isset($_SESSION['user_id'])) 
    {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Não autorizado']);
        exit;
    }
    
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';
    
    if (!file_exists($uploadDir)) 
    {
        mkdir($uploadDir, 0777, true);
    }
    
    if (isset($_POST['webcam_image'])) 
    {
        // Upload via webcam (base64)
        $img = $_POST['webcam_image'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        
        // Gera um nome único para o ficheiro
        $filename = uniqid() . '.png';
        $filepath = '/public/uploads/' . $filename;
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filepath;
        
        // Guarda a imagem
        file_put_contents($fullPath, $data);
        
        if (isset($_POST['overlay_id']) && !empty($_POST['overlay_id'])) 
        {
            $overlay_id = (int)$_POST['overlay_id'];
            if ($this->overlayModel->getOverlayById($overlay_id)) 
            {
                $this->applyOverlay($fullPath, $_SERVER['DOCUMENT_ROOT'] . $this->overlayModel->filepath);
            }
        }

        if (isset($_POST['filter']) && !empty($_POST['filter'])) 
        {
            $filter = $_POST['filter'];
            $this->applyFilter($fullPath, $filter);
        }
    } 
    elseif (isset($_FILES['file_image'])) 
    {
        $file = $_FILES['file_image'];
        
        // Verifica o tipo de ficheiro
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) 
        {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Tipo de ficheiro não permitido']);
            exit;
        }
        
        // Gera um nome único para o ficheiro
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = '/public/uploads/' . $filename;
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filepath;
        
        // Move o ficheiro carregado
        move_uploaded_file($file['tmp_name'], $fullPath);
        
        // Aplica a sobreposição se fornecida
        if (isset($_POST['overlay_id']) && !empty($_POST['overlay_id'])) 
        {
            $overlay_id = (int)$_POST['overlay_id'];
            if ($this->overlayModel->getOverlayById($overlay_id)) 
            {
                $this->applyOverlay($fullPath, $_SERVER['DOCUMENT_ROOT'] . $this->overlayModel->filepath);
            }
        }
        
        if (isset($_POST['filter']) && !empty($_POST['filter'])) 
        {
            $filter = $_POST['filter'];
            $this->applyFilter($fullPath, $filter);
        }
    } 
    else 
    {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Nenhuma imagem fornecida']);
        exit;
    }
    
        // Guarda a imagem na base de dados
        if ($this->imageModel->saveImage($_SESSION['user_id'], $filepath)) 
        {

            header('Content-Type: text/plain');
            echo "success";
            exit;
        } 
        else 
        {
            header('Content-Type: text/plain');
            echo "error";
            exit;
        }
    
    exit;
}

// Aplica uma imagem de sobreposição a outra imagem
    private function applyOverlay($baseImagePath, $overlayPath) 
    {
        // Carrega as imagens
        $extension = pathinfo($baseImagePath, PATHINFO_EXTENSION);
        if ($extension === 'png') 
        {
            $baseImage = imagecreatefrompng($baseImagePath);
        } 
        elseif ($extension === 'jpg' || $extension === 'jpeg') 
        {
            $baseImage = imagecreatefromjpeg($baseImagePath);
        } 
        elseif ($extension === 'gif') 
        {
            $baseImage = imagecreatefromgif($baseImagePath);
        } 
        else 
        {
            return false;
        }
        
        $overlay = imagecreatefrompng($overlayPath); // Assume que todas as sobreposições são PNG com transparência
        
        // Obtém as dimensões das imagens
        $baseWidth = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);
        $overlayWidth = imagesx($overlay);
        $overlayHeight = imagesy($overlay);
        
        // Redimensiona a sobreposição para se ajustar à imagem base, mantendo a proporção
        $newWidth = $baseWidth / 2; // Exemplo: metade da largura da imagem base
        $newHeight = $overlayHeight * ($newWidth / $overlayWidth);
        
        // Posição para centrar a sobreposição
        $posX = ($baseWidth - $newWidth) / 2;
        $posY = ($baseHeight - $newHeight) / 2;
        
        // Combina as imagens
        imagecopyresampled(
            $baseImage, $overlay,
            (int)$posX, (int)$posY, 0, 0,
            (int)$newWidth, (int)$newHeight, $overlayWidth, $overlayHeight
        );
        // Guarda a imagem combinada
        if ($extension === 'png') 
        {
            imagepng($baseImage, $baseImagePath);
        } 
        elseif ($extension === 'jpg' || $extension === 'jpeg') 
        {
            imagejpeg($baseImage, $baseImagePath);
        } 
        elseif ($extension === 'gif') 
        {
            imagegif($baseImage, $baseImagePath);
        }
        
        // Liberta a memória
        imagedestroy($baseImage);
        imagedestroy($overlay);
        
        return true;
    }
    
    // Apaga uma imagem
    public function delete() 
    {
        // Verifica se o utilizador está autenticado
        if (!isset($_SESSION['user_id'])) 
        {
            $this->redirect('/?controller=user&action=login');
            return;
        }
        
        // Verifica se o ID da imagem foi fornecido
        if (!isset($_GET['id'])) 
        {
            $this->redirect('/?controller=editor');
            return;
        }
        
        $image_id = (int)$_GET['id'];
        
        // Tenta apagar a imagem
        if ($this->imageModel->deleteImage($image_id, $_SESSION['user_id'])) 
        {
            // Redireciona de volta para o editor
            $this->redirect('/?controller=editor&success=deleted');
        } 
        else 
        {
            // Redireciona com erro
            $this->redirect('/?controller=editor&error=delete_failed');
        }
    }
}