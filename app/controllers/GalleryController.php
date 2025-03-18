<?php
 

class GalleryController extends BaseController 
{
    private $imageModel;
    
    public function __construct() 
    {
        $this->imageModel = new Image();
    }
    
    // Página principal da galeria
    public function index() 
    {
        // Determina a página atual
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Garante que a página é pelo menos 1
        
        // Número de imagens por página
        $limit = 5;
        
        // Obtém as imagens para a página atual
        $images = $this->imageModel->getAllImages($page, $limit);
        
        // Processa dados adicionais para cada imagem
        foreach ($images as &$image) 
        {
            $image['likes_count'] = $this->imageModel->countLikes($image['id']);
            $image['comments_count'] = $this->imageModel->countComments($image['id']);
            
            // Verifica se o utilizador atual deu like, se estiver autenticado
            if (isset($_SESSION['user_id'])) 
            {
                $image['is_liked'] = $this->imageModel->isLikedByUser($image['id'], $_SESSION['user_id']);
            } 
            else 
            {
                $image['is_liked'] = false;
            }
        }
        
        // Calcula informações para paginação
        $total_images = $this->imageModel->countAllImages();
        $total_pages = ceil($total_images / $limit);
        
        // Renderiza a vista
        $this->render('gallery/index', [
            'pageTitle' => 'Galeria',
            'images' => $images,
            'currentPage' => $page,
            'totalPages' => $total_pages
        ]);
    }
    
    // Visualiza uma imagem específica
    public function view() 
    {
        if (!isset($_GET['id'])) 
        {
            $this->redirect('/?controller=gallery');
            return;
        }
        
        $image_id = (int)$_GET['id'];
        
        // Obtém os detalhes da imagem
        if (!$this->imageModel->getImageById($image_id)) 
        {
            // Imagem não encontrada
            $this->render('error/404', ['pageTitle' => 'Imagem não encontrada']);
            return;
        }
        
        // Obtém informações adicionais
        $image = [
            'id' => $this->imageModel->id,
            'user_id' => $this->imageModel->user_id,
            'filepath' => $this->imageModel->filepath,
            'created_at' => $this->imageModel->created_at,
            'username' => $this->imageModel->username,
            'likes_count' => $this->imageModel->countLikes($image_id),
            'comments' => $this->imageModel->getComments($image_id)
        ];
        
        // Verifica se o utilizador atual deu like
        if (isset($_SESSION['user_id'])) 
        {
            $image['is_liked'] = $this->imageModel->isLikedByUser($image_id, $_SESSION['user_id']);
        } 
        else 
        {
            $image['is_liked'] = false;
        }
        
        // Verifica se o utilizador atual é o dono da imagem
        $image['is_owner'] = isset($_SESSION['user_id']) && $this->imageModel->user_id == $_SESSION['user_id'];
        
        // Renderiza a vista
        $this->render('gallery/view', [
            'pageTitle' => 'Ver Imagem',
            'image' => $image
        ]);
    }
    
    // Adiciona ou remove um like
    public function like() 
    {
        // Verifica se o utilizador está autenticado
        if (!isset($_SESSION['user_id'])) 
        {
            // Redireciona para a página de login
            $this->redirect('/?controller=user&action=login');
            return;
        }
        
        // Verifica se o ID da imagem foi fornecido
        if (!isset($_POST['image_id'])) 
        {
            $this->redirect('/?controller=gallery');
            return;
        }
        
        $image_id = (int)$_POST['image_id'];
        $user_id = $_SESSION['user_id'];
        
        // Adiciona ou remove o like
        $this->imageModel->toggleLike($image_id, $user_id);
        
        // Se for uma requisição AJAX, retorna o novo número de likes
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') 
        {
            $likes_count = $this->imageModel->countLikes($image_id);
            $is_liked = $this->imageModel->isLikedByUser($image_id, $user_id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'likes_count' => $likes_count,
                'is_liked' => $is_liked
            ]);
            exit;
        }
        
        // Redireciona de volta para a página da imagem
        $this->redirect('/?controller=gallery&action=view&id=' . $image_id);
    }
    
    // Adiciona um comentário
    public function comment() 
    {
        // Verifica se o utilizador está autenticado
        if (!isset($_SESSION['user_id'])) 
        {
            // Redireciona para a página de login
            $this->redirect('/?controller=user&action=login');
            return;
        }
        
        // Verifica se os dados do formulário foram enviados
        if (!isset($_POST['image_id']) || !isset($_POST['comment']) || empty($_POST['comment'])) 
        {
            $this->redirect('/?controller=gallery');
            return;
        }
        
        $image_id = (int)$_POST['image_id'];
        $user_id = $_SESSION['user_id'];
        $comment = htmlspecialchars($_POST['comment']);
        
        // Adiciona o comentário
        $this->imageModel->addComment($image_id, $user_id, $comment);
        
        // Redireciona de volta para a página da imagem
        $this->redirect('/?controller=gallery&action=view&id=' . $image_id);
    }
}