<?php
 

class GalleryController extends BaseController 
{
    private $imageModel;
    
    public function __construct() 
    {
        $this->imageModel = new Image();
    }
    

    
    public function index() 
    {

        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Garante que a página é pelo menos 1
        

        
        $limit = 5;
        

        
        $images = $this->imageModel->getAllImages($page, $limit);
        

        
        foreach ($images as &$image) 
        {
            $image['likes_count'] = $this->imageModel->countLikes($image['id']);
            $image['comments_count'] = $this->imageModel->countComments($image['id']);
            
       
            
            if (isset($_SESSION['user_id'])) 
            {
                $image['is_liked'] = $this->imageModel->isLikedByUser($image['id'], $_SESSION['user_id']);
            } 
            else 
            {
                $image['is_liked'] = false;
            }
        }
        

        
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
    

    
    public function view() 
    {
        if (!isset($_GET['id'])) 
        {
            $this->redirect('/?controller=gallery');
            return;
        }
        
        $image_id = (int)$_GET['id'];
        

        
        if (!$this->imageModel->getImageById($image_id)) 
        {
 
            
            $this->render('error/404', ['pageTitle' => 'Imagem não encontrada']);
            return;
        }
        

        
        $image = [
            'id' => $this->imageModel->id,
            'user_id' => $this->imageModel->user_id,
            'filepath' => $this->imageModel->filepath,
            'created_at' => $this->imageModel->created_at,
            'username' => $this->imageModel->username,
            'likes_count' => $this->imageModel->countLikes($image_id),
            'comments' => $this->imageModel->getComments($image_id)
        ];
        

        
        if (isset($_SESSION['user_id'])) 
        {
            $image['is_liked'] = $this->imageModel->isLikedByUser($image_id, $_SESSION['user_id']);
        } 
        else 
        {
            $image['is_liked'] = false;
        }
        

        
        $image['is_owner'] = isset($_SESSION['user_id']) && $this->imageModel->user_id == $_SESSION['user_id'];
        

        
        $this->render('gallery/view', [
            'pageTitle' => 'Ver Imagem',
            'image' => $image
        ]);
    }
    

    
    public function like() 
    {
   
        
        if (!isset($_SESSION['user_id'])) 
        {
       
            
            $this->redirect('/?controller=user&action=login');
            return;
        }
        

        
        if (!isset($_POST['image_id'])) 
        {
            $this->redirect('/?controller=gallery');
            return;
        }
        
        $image_id = (int)$_POST['image_id'];
        $user_id = $_SESSION['user_id'];
        

        
        $this->imageModel->toggleLike($image_id, $user_id);
        
    
        
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

        
        $this->redirect('/?controller=gallery&action=view&id=' . $image_id);
    }
    

    
    public function comment() 
    {
 
        
        if (!isset($_SESSION['user_id'])) 
        {
     
            
            $this->redirect('/?controller=user&action=login');
            return;
        }
        

        
        if (!isset($_POST['image_id']) || !isset($_POST['comment']) || empty($_POST['comment'])) 
        {
            $this->redirect('/?controller=gallery');
            return;
        }
        
        $image_id = (int)$_POST['image_id'];
        $user_id = $_SESSION['user_id'];
        $comment = htmlspecialchars($_POST['comment']);
        

        
        $this->imageModel->addComment($image_id, $user_id, $comment);
        
     
        
        $this->redirect('/?controller=gallery&action=view&id=' . $image_id);
    }
}