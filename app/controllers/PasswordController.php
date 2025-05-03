<?php

class PasswordController {

    // Mostra o formulário de pedido de recuperação
    public function request() {
        include 'views/password_request.php';
    }

    // Processa o pedido de recuperação
    public function send() {
        global $pdo;
        $email = $_POST['email'] ?? '';
        $success = null;
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email inválido.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(16));
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_created_at = NOW() WHERE id = :id");
                $stmt->execute(['token' => $token, 'id' => $user['id']]);

                // Enviar email com o link de reset
                $link = "http://localhost:8000/?page=reset&token=$token";
                $subject = "Recuperação de Password Camagru";
                $message = "Clique no link para redefinir a sua password: $link";
                mail($email, $subject, $message);

                $success = "Email de recuperação enviado.";
            } else {
                $errors[] = "Email não encontrado.";
            }
        }

        include 'views/password_request.php';
    }

 
    public function reset() {
        global $pdo;
        $token = $_GET['token'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("Token inválido.");
        }

        include 'views/password_reset.php';
    }

    // Processa a nova password
    public function update() {
        global $pdo;
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $errors = [];
        $success = null;

        if ($password !== $password_confirm) {
            $errors[] = "As passwords não coincidem.";
        } elseif (strlen($password) < 4) {
            $errors[] = "A password deve ter pelo menos 4 caracteres.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verificar se é igual à password antiga
                if (password_verify($password, $user['password_hash'])) {
                    $errors[] = "A nova password não pode ser igual à atual.";
                    include 'views/password_reset.php';
                    return;
                }
            
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash, reset_token = NULL, reset_token_created_at = NULL WHERE id = :id");
                $stmt->execute(['hash' => $hash, 'id' => $user['id']]);
            
                // Redirecionar
                header("Location: ?page=login&message=password_reset_success");
                exit;
            } else {
                $errors[] = "Token inválido.";
            }
            
        }

        include 'views/password_reset.php';
    }
}
?>
