<?php
class ProfileController {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=login");
            exit;
        }

        global $pdo;
        $success = null;
        $errors = [];

        // Busca dados atuais do utilizador
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Atualizar username/email
        if (isset($_POST['update_profile'])) {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if (empty($username) || empty($email)) {
                $errors[] = "Todos os campos são obrigatórios.";
            } else {
                // Verificar se outro utilizador já usa este username/email
                $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id");
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'id' => $_SESSION['user_id']
                ]);

                if ($stmt->fetch()) {
                    $errors[] = "Username ou email já em uso.";
                } else {
                    // Atualizar
                    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
                    $stmt->execute([
                        'username' => $username,
                        'email' => $email,
                        'id' => $_SESSION['user_id']
                    ]);

                    $_SESSION['username'] = $username;
                    $success = "Dados atualizados com sucesso.";
                }
            }
        }

        // Alterar password
        if (isset($_POST['change_password'])) {
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (empty($password) || empty($password_confirm)) {
                $errors[] = "Preenche ambos os campos da nova password.";
            } elseif ($password !== $password_confirm) {
                $errors[] = "As passwords não coincidem.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
                $stmt->execute([
                    'password_hash' => $password_hash,
                    'id' => $_SESSION['user_id']
                ]);

                $success = "Password alterada com sucesso.";
            }
        }

 
        // Atualizar notificações
        if (isset($_POST['toggle_notifications'])) {
            $notifications = (isset($_POST['notifications']) && $_POST['notifications'] == '1') ? true : false;

            $stmt = $pdo->prepare("UPDATE users SET notification_enabled = :notifications WHERE id = :id");
            $stmt->bindValue(':notifications', $notifications, PDO::PARAM_BOOL); 
            $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $success = "Preferências de notificações atualizadas.";
        }


        // Atualizar os dados para a view depois de possíveis alterações
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        include 'views/profile.php';
    }
}
?>
