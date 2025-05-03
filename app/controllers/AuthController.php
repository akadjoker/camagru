<?php
class AuthController {

    private $errors = [];

    public function register() {
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
    
            // Validações
            if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
                $errors[] = "Todos os campos são obrigatórios.";
            }
            if ($password !== $password_confirm) {
                $errors[] = "As passwords não coincidem.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email inválido.";
            }
    
            if (empty($errors)) {
                global $pdo;
    
                // Verifica duplicados
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username' => $username, 'email' => $email]);
                if ($stmt->fetch()) {
                    $errors[] = "Username ou email já em uso.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
                    // Insere utilizador
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
                    $stmt->execute([
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => $password_hash
                    ]);
    
                    // ENVIO DO EMAIL DE CONFIRMAÇÃO
                    $confirm_link = "http://localhost:8000/?page=confirm&email=" . urlencode($email);
    
                    $subject = "Confirma o teu email no Camagru";
                    $message = "Olá $username,\n\nPor favor confirma o teu email clicando no link abaixo:\n$confirm_link\n\nObrigado!";
                    $headers = "From: no-reply@camagru.local";
    
                    mail($email, $subject, $message, $headers);
    
                    // Redirecionar para login com mensagem de sucesso
                    header("Location: ?page=login&success=1");
                    exit;
                }
            }
        }
    
        $this->errors = $errors;
        include 'views/register.php';
    }
    

    public function login() {
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
    
            if (empty($username) || empty($password)) {
                $errors[] = "Preenche todos os campos.";
            } else {
                global $pdo;
    
                // Busca o utilizador pelo username
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($user && password_verify($password, $user['password_hash'])) {
    
                    if (!$user['confirmed']) {
                        // Email ainda não confirmado
                        $errors[] = "Precisas de confirmar o teu email antes de fazer login. 
                        <a href='?page=confirm&email=" . urlencode($user['email']) . "'>Reenviar email de confirmação</a>";
                    } else {
                        // Login bem-sucedido
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
    
                        header("Location: ?page=profile");
                        exit;
                    }
    
                } else {
                    $errors[] = "Username ou password incorretos.";
                }
            }
        }
    
        // Passa os erros para a view
        $this->errors = $errors;
    
        // Carrega a view
        include 'views/login.php';
    }
    
    

    public function confirm() {
        $email = $_GET['email'] ?? '';
        $success = false;
    
        if (!empty($email)) {
            global $pdo;
    
            // Verifica se existe o utilizador
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                // Atualiza o campo confirmed
                $stmt = $pdo->prepare("UPDATE users SET confirmed = true WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $success = true;
            }
        }
    
        include 'views/confirm.php';
    }
    

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ?page=home");
        exit;
    }
    

    public function reset() {
        echo "Página de redefinição (a implementar).";
    }
}
?>
