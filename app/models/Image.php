<?php
// app/models/Image.php

class Image extends BaseModel 
{
    private $table = 'user_images';
    
    // Propriedades
    public $id;
    public $user_id;
    public $filepath;
    public $created_at;
    public $username; // Para juntar com o utilizador
    public $likes_count; // Para contar likes
    public $comments_count; // Para contar comentários
    public $is_liked; // Para verificar se o utilizador atual deu like
    
    // Obtém todas as imagens para a galeria (com paginação)
    public function getAllImages($page = 1, $limit = 5) 
    {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT i.*, u.username 
                  FROM " . $this->table . " i
                  JOIN users u ON i.user_id = u.id
                  ORDER BY i.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Conta o total de imagens para paginação
    public function countAllImages() 
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->db->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
    
    // Obtém uma imagem pelo ID
    public function getImageById($id) 
    {
        $query = "SELECT i.*, u.username 
                  FROM " . $this->table . " i
                  JOIN users u ON i.user_id = u.id
                  WHERE i.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->filepath = $row['filepath'];
            $this->created_at = $row['created_at'];
            $this->username = $row['username'];
            return true;
        }
        
        return false;
    }
    
    // Verifica se o utilizador deu like na imagem
    public function isLikedByUser($image_id, $user_id) 
    {
        $query = "SELECT id FROM likes 
                  WHERE image_id = :image_id AND user_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Conta o número de likes para uma imagem
    public function countLikes($image_id) 
    {
        $query = "SELECT COUNT(*) as total FROM likes WHERE image_id = :image_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
    // Adiciona ou remove um like
    public function toggleLike($image_id, $user_id) 
    {
        if ($this->isLikedByUser($image_id, $user_id)) 
        {
            // Remove o like
            $query = "DELETE FROM likes 
                      WHERE image_id = :image_id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':image_id', $image_id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } 
        else 
        {
            // Adiciona o like
            $query = "INSERT INTO likes (image_id, user_id) 
                      VALUES (:image_id, :user_id)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':image_id', $image_id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        }
    }
    
    // Obtém os comentários de uma imagem
    public function getComments($image_id) 
    {
        $query = "SELECT c.*, u.username 
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.image_id = :image_id
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Adiciona um comentário
    public function addComment($image_id, $user_id, $comment) 
    {
        $query = "INSERT INTO comments (image_id, user_id, comment) 
                  VALUES (:image_id, :user_id, :comment)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':comment', $comment);
        
        if ($stmt->execute()) 
        {
            // Notifica o dono da imagem se tiver notificações ativadas
            $this->notifyImageOwner($image_id, $user_id);
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    // Notifica o dono da imagem sobre um novo comentário
    private function notifyImageOwner($image_id, $commenter_id) 
    {
        // Obtém informações da imagem e do dono
        $query = "SELECT i.user_id, u.email, u.notification_enabled, u2.username as commenter_username
                  FROM " . $this->table . " i
                  JOIN users u ON i.user_id = u.id
                  JOIN users u2 ON u2.id = :commenter_id
                  WHERE i.id = :image_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->bindParam(':commenter_id', $commenter_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Não notifica se for o próprio utilizador a comentar a sua imagem
            if ($row['user_id'] == $commenter_id) 
            {
                return;
            }
            
            // Verifica se o utilizador tem notificações ativadas
            if ($row['notification_enabled']) 
            {
                $subject = "Novo comentário na tua imagem - Camagru";
                $message = "Olá,\n\nO utilizador {$row['commenter_username']} comentou numa das tuas imagens no Camagru.\n\nVê o comentário em: http://localhost:8000/?controller=gallery&action=view&id={$image_id}";
                $headers = "From: noreply@camagru.com";
                
                mail($row['email'], $subject, $message, $headers);
            }
        }
    }
    
    // Conta o número de comentários para uma imagem
    public function countComments($image_id) 
    {
        $query = "SELECT COUNT(*) as total FROM comments WHERE image_id = :image_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }


// Salva uma imagem na base de dados
public function saveImage($user_id, $filepath) 
{
    $query = "INSERT INTO " . $this->table . " (user_id, filepath) 
              VALUES (:user_id, :filepath)";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':filepath', $filepath);
    
    if ($stmt->execute()) 
    {
        $this->id = $this->db->lastInsertId();
        $this->user_id = $user_id;
        $this->filepath = $filepath;
        $this->created_at = date('Y-m-d H:i:s');
        return true;
    }
    
    return false;
}

// Apaga uma imagem
public function deleteImage($id, $user_id) 
{
    // Primeiro verifica se a imagem pertence ao utilizador
    $query = "SELECT filepath FROM " . $this->table . " 
              WHERE id = :id AND user_id = :user_id";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) 
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $filepath = $row['filepath'];
        
        // Apaga a imagem do sistema de ficheiros
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filepath)) 
        {
            unlink($_SERVER['DOCUMENT_ROOT'] . $filepath);
        }
        
        // Apaga os registos relacionados (likes e comentários)
        $query = "DELETE FROM likes WHERE image_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $query = "DELETE FROM comments WHERE image_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Apaga a imagem da base de dados
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    return false;
}

// Obtém as imagens de um utilizador específico
public function getUserImages($user_id) 
{
    $query = "SELECT * FROM " . $this->table . " 
              WHERE user_id = :user_id 
              ORDER BY created_at DESC";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getRandomImages($limit = 3) 
{
    $query = "SELECT i.*, u.username 
              FROM " . $this->table . " i
              JOIN users u ON i.user_id = u.id
              ORDER BY RANDOM() 
              LIMIT :limit";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}