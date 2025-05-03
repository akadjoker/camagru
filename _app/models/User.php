<?php
// Modelo de utilizador

class User extends BaseModel 
{
    private $table = 'users';
    
    // Propriedades
    public $id;
    public $username;
    public $email;
    public $password;
    public $password_hash; // Nova propriedade para armazenar o hash da password
    public $verified;
    public $verification_token;
    public $reset_token;
    public $reset_token_expiry;
    public $notification_enabled;
    public $created_at;
    
    // Regista um novo utilizador
    public function register() 
    {
        $query = "INSERT INTO " . $this->table . " 
                (username, email, password, verification_token) 
                VALUES (:username, :email, :password, :verification_token)";
    
        $stmt = $this->db->prepare($query);
        
        // Sanitização
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Hash da palavra-passe
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Gera token para verificação
        $this->verification_token = bin2hex(random_bytes(50));
        
        // Vincula os parâmetros
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":verification_token", $this->verification_token);

        error_log("Token gerado: " . $this->verification_token);
        
        // Executa a query
        if ($stmt->execute()) 
        {
            return true;
        }
        
        return false;
    }
    
    // Verifica se o utilizador existe pelo nome de utilizador ou email
    public function userExists($isRegistration = true) 
    {
        $query = "SELECT id, username, email, password, verified, verification_token FROM " . $this->table . " 
                  WHERE username = :username OR email = :email";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            if ($isRegistration) 
            {
                return true; // Utilizador já existe
            } 
            else 
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->password_hash = $row['password']; // Guarda o hash em propriedade separada
                $this->verified = $row['verified'];
                $this->verification_token = $row['verification_token'];
                return true;
            }
        }
        
        return false;
    }
    
    // Verifica a conta do utilizador com o token
    public function verifyAccount($token) 
    {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE verification_token = :token";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Atualiza para verificado e limpa o token
            $query = "UPDATE " . $this->table . " 
                      SET verified = true, verification_token = NULL 
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $row['id']);
            
            if ($stmt->execute()) 
            {
                return true;
            }
        }
        
        return false;
    }
    
    // Login do utilizador
    public function login() 
    {
        if ($this->userExists(false)) 
        {
            // Verifica se a conta está verificada
            if (!$this->verified) 
            {
                return false;
            }
            
            // Verifica a password
            if (password_verify($this->password, $this->password_hash)) 
            {
                return true;
            }
        }
        
        return false;
    }
    
    // Recupera um utilizador pelo ID
    public function getUserById($id) 
    {
        $query = "SELECT id, username, email, verified, notification_enabled 
                  FROM " . $this->table . " 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->verified = $row['verified'];
            $this->notification_enabled = $row['notification_enabled'];
            return true;
        }
        
        return false;
    }
    
    // Atualiza os dados do utilizador
    public function updateUser() 
    {
        $query = "UPDATE " . $this->table . " SET 
                  username = :username, 
                  email = :email, 
                  notification_enabled = :notification_enabled 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Sanitização
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Vincula os parâmetros
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":notification_enabled", $this->notification_enabled);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) 
        {
            return true;
        }
        
        return false;
    }
    
    // Gera token para recuperação de password
    public function generatePasswordResetToken() 
    {
        $this->reset_token = bin2hex(random_bytes(50));
        $this->reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $query = "UPDATE " . $this->table . " 
                  SET reset_token = :reset_token, reset_token_expiry = :reset_token_expiry 
                  WHERE email = :email";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":reset_token", $this->reset_token);
        $stmt->bindParam(":reset_token_expiry", $this->reset_token_expiry);
        $stmt->bindParam(":email", $this->email);
        
        if ($stmt->execute()) 
        {
            return true;
        }
        
        return false;
    }
    
    // Verifica se o token de reset é válido
    public function isValidResetToken($token) 
    {
        $query = "SELECT id, email FROM " . $this->table . " 
                  WHERE reset_token = :token AND reset_token_expiry > NOW()";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->email = $row['email'];
            return true;
        }
        
        return false;
    }
    
    // Atualiza a password
    public function updatePassword($newPassword) 
    {
        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $query = "UPDATE " . $this->table . " 
                  SET password = :password, reset_token = NULL, reset_token_expiry = NULL 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) 
        {
            return true;
        }
        
        return false;
    }
    
    // Alterna a preferência de notificação
    public function toggleNotification() 
    {
        $query = "UPDATE " . $this->table . " 
                  SET notification_enabled = NOT notification_enabled 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) 
        {
            // Atualiza o valor local
            $this->notification_enabled = !$this->notification_enabled;
            return true;
        }
        
        return false;
    }

    // Adicionar ao modelo User
public function changePassword($currentPassword, $newPassword) 
{
    // Verifica se a password atual está correta
    if (!password_verify($currentPassword, $this->password_hash)) 
    {
        return false;
    }
    
    // Hash da nova password
    $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
    
    // Atualiza a password na base de dados
    $query = "UPDATE " . $this->table . " 
              SET password = :password 
              WHERE id = :id";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":password", $password_hash);
    $stmt->bindParam(":id", $this->id);
    
    if ($stmt->execute()) 
    {
        return true;
    }
    
    return false;
}
}