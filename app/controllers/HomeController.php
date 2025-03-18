<?php
 

class HomeController extends BaseController 
{
  public function index() 
{
    $pageTitle = 'Bem-vindo ao Camagru';
    
    $dbTest = new Database();
    $conn = $dbTest->getConnection();
    $dbStatus = $conn ? 'Conexão com o banco de dados: OK' : 'Erro na conexão com o banco de dados';
    
    $imageModel = new Image();
    
    $recentImages = $imageModel->getRandomImages(3);
    
    foreach ($recentImages as &$image) 
    {
        $image['likes_count'] = $imageModel->countLikes($image['id']);
        $image['comments_count'] = $imageModel->countComments($image['id']);
    }

    $this->render('home/index', 
[
        'pageTitle' => $pageTitle,
        'dbStatus' => $dbStatus,
        'recentImages' => $recentImages
    ]);
}
}



