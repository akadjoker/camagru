<?php
 
class Overlay extends BaseModel 
{
    private $table = 'overlay_images';
    
 
    public $id;
    public $name;
    public $filepath;
    public $created_at;
    
 
    public function getAllOverlays() 
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id";
        $stmt = $this->db->query($query);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //   sobreposição pelo ID
    public function getOverlayById($id) 
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->filepath = $row['filepath'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
}