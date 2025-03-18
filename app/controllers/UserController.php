<?php
// Controlador de utilizadores

class UserController extends BaseController 
{
    private $userModel;
    
    public function __construct() 
    {
        $this->userModel = new User();
    }
    
    // Página de registo
    public function register() 
    {
        // Se já estiver autenticado, redireciona para a página inicial
        if (isset($_SESSION['user_id'])) 
        {
            $this->redirect('/');
            return;
        }
        
        // Se não for um pedido POST, mostra o formulário
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            $this->render('user/register', ['pageTitle' => 'Registo']);
            return;
        }
        
        // Validação dos dados do formulário
        $errors = [];
        
        // Valida nome de utilizador
        if (empty($_POST['username'])) 
        {
            $errors['username'] = 'O nome de utilizador é obrigatório';
        } 
        elseif (strlen($_POST['username']) < 3) 
        {
            $errors['username'] = 'O nome de utilizador deve ter pelo menos 3 caracteres';
        }
        
        // Valida email
        if (empty($_POST['email'])) 
        {
            $errors['email'] = 'O email é obrigatório';
        } 
        elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        {
            $errors['email'] = 'Introduz um email válido';
        }
        
        // Valida password
        if (empty($_POST['password'])) 
        {
            $errors['password'] = 'A password é obrigatória';
        } 
        elseif (strlen($_POST['password']) < 6) 
        {
            $errors['password'] = 'A password deve ter pelo menos 6 caracteres';
        }
        
        // Valida confirmação de password
        if ($_POST['password'] !== $_POST['confirm_password']) 
        {
            $errors['confirm_password'] = 'As passwords não coincidem';
        }
        
        // Se existem erros, volta ao formulário
        if (!empty($errors)) 
        {
            $this->render('user/register', [
                'pageTitle' => 'Registo',
                'errors' => $errors,
                'oldInput' => $_POST
            ]);
            return;
        }
        
        // Atribui os valores ao modelo
        $this->userModel->username = $_POST['username'];
        $this->userModel->email = $_POST['email'];
        $this->userModel->password = $_POST['password'];
        
        // Verifica se o utilizador já existe
        if ($this->userModel->userExists()) 
        {
            $this->render('user/register', [
                'pageTitle' => 'Registo',
                'error' => 'Este nome de utilizador ou email já está registado',
                'oldInput' => $_POST
            ]);
            return;
        }
        
        // Regista o utilizador
        if ($this->userModel->register()) 
        {
            error_log("Token gerado para {$this->userModel->email}: {$this->userModel->verification_token}");
  
            // Envia email de verificação
            $this->sendVerificationEmail($this->userModel->email, $this->userModel->verification_token);
            
            // Redireciona para a página de sucesso
            $this->redirect('/?controller=user&action=registerSuccess');
        } 
        else 
        {
            $this->render('user/register', [
                'pageTitle' => 'Registo',
                'error' => 'Ocorreu um erro ao registar. Tenta novamente.',
                'oldInput' => $_POST
            ]);
        }
    }
    
    // Página de sucesso após registo
    public function registerSuccess() 
    {
        $this->render('user/register-success', ['pageTitle' => 'Registo Concluído']);
    }
    
 
    private function sendVerificationEmail($email, $token) 
    {
        $subject = "Verificação de Conta - Camagru";
        $verify_link = "http://localhost:8000/?controller=user&action=verify&token=$token";
        
        $message = "
        <html>
        <head>
            <title>Verificação de Conta - Camagru</title>
        </head>
        <body>
            <h2>Bem-vindo ao Camagru!</h2>
            <p>Clica no link abaixo para verificar a tua conta:</p>
            <p><a href='$verify_link'>Verificar a minha conta</a></p>
            <p>Se o link não funcionar, copia e cola este URL no teu navegador:</p>
            <p>$verify_link</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@camagru.com" . "\r\n";
        
        return mail($email, $subject, $message, $headers);
    }
    // Verifica a conta com o token
    public function verify() 
    {
        if (!isset($_GET['token'])) 
        {
            $this->redirect('/');
            return;
        }
        
        $token = $_GET['token'];
        
        if ($this->userModel->verifyAccount($token)) 
        {
            $this->render('user/verify-success', ['pageTitle' => 'Conta Verificada']);
        } 
        else 
        {
            $this->render('user/verify-error', ['pageTitle' => 'Erro de Verificação']);
        }
    }
    
    // Página de login
    public function login() 
    {
        // Se já estiver autenticado, redireciona para a página inicial
        if (isset($_SESSION['user_id'])) 
        {
            $this->redirect('/');
            return;
        }
        
        // Se não for um pedido POST, mostra o formulário
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            $this->render('user/login', ['pageTitle' => 'Login']);
            return;
        }
        
        // Valida os dados
        $errors = [];
        
        if (empty($_POST['username'])) 
        {
            $errors['username'] = 'Introduz o teu nome de utilizador ou email';
        }
        
        if (empty($_POST['password'])) 
        {
            $errors['password'] = 'Introduz a tua password';
        }
        
        if (!empty($errors)) 
        {
            $this->render('user/login', [
                'pageTitle' => 'Login',
                'errors' => $errors,
                'oldInput' => $_POST
            ]);
            return;
        }
        
        // Atribui valores ao modelo
        $this->userModel->username = $_POST['username'];
        $this->userModel->email = $_POST['username']; // Permite login com email ou username
        $this->userModel->password = $_POST['password'];
        
        // Tenta fazer login
        if ($this->userModel->login()) 
        {
            // Guarda o ID na sessão
            $_SESSION['user_id'] = $this->userModel->id;
            $_SESSION['username'] = $this->userModel->username;
            
            // Redireciona para a página inicial
            $this->redirect('/');
        } 
        else 
        {
            $this->render('user/login', [
                'pageTitle' => 'Login',
                'error' => 'Nome de utilizador ou password incorretos, ou a conta não está verificada',
                'oldInput' => $_POST
            ]);
        }
    }
    
    // Sair da conta
    public function logout() 
    {
        // Limpa as variáveis de sessão
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        
        // Destrói a sessão
        session_destroy();
        
        // Redireciona para a página inicial
        $this->redirect('/');
    }
    
    // Página de recuperação de password
    public function forgotPassword() 
    {
        // Se já estiver autenticado, redireciona
        if (isset($_SESSION['user_id'])) 
        {
            $this->redirect('/');
            return;
        }
        
        // Se não for um pedido POST, mostra o formulário
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            $this->render('user/forgot-password', ['pageTitle' => 'Recuperar Password']);
            return;
        }
        
        // Valida email
        if (empty($_POST['email'])) 
        {
            $this->render('user/forgot-password', [
                'pageTitle' => 'Recuperar Password',
                'error' => 'Introduz o teu email',
                'oldInput' => $_POST
            ]);
            return;
        }
        
        $this->userModel->email = $_POST['email'];
        
        // Verifica se o email existe
        if ($this->userModel->userExists(false)) 
        {
            // Gera token de recuperação
            if ($this->userModel->generatePasswordResetToken()) 
            {
                // Envia email com link de recuperação
                $this->sendPasswordResetEmail($this->userModel->email, $this->userModel->reset_token);
                
                // Mostra mensagem de sucesso
                $this->render('user/forgot-password-success', ['pageTitle' => 'Email Enviado']);
                return;
            }
        }
        
        // Não mostra erro para não revelar se o email existe ou não (segurança)
        $this->render('user/forgot-password-success', ['pageTitle' => 'Email Enviado']);
    }
    
    // Envia email de recuperação de password
    private function sendPasswordResetEmail($email, $token) 
    {
        $subject = "Recuperação de Password - Camagru";
        $reset_link = "http://localhost:8000/?controller=user&action=resetPassword&token=$token";
        $message = "Clica no link seguinte para redefinir a tua password: $reset_link\n\nEste link expira em 1 hora.";
        $headers = "From: noreply@camagru.com";
        
        mail($email, $subject, $message, $headers);
    }
    
    // Página de redefinição de password
    public function resetPassword() 
    {
        // Se já estiver autenticado, redireciona
        if (isset($_SESSION['user_id'])) 
        {
            $this->redirect('/');
            return;
        }
        
        // Verifica se o token foi fornecido
        if (!isset($_GET['token'])) 
        {
            $this->redirect('/');
            return;
        }
        
        $token = $_GET['token'];
        
        // Verifica se o token é válido
        if (!$this->userModel->isValidResetToken($token)) 
        {
            $this->render('user/reset-password-error', ['pageTitle' => 'Link Inválido']);
            return;
        }
        
        // Se não for um pedido POST, mostra o formulário
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            $this->render('user/reset-password', [
                'pageTitle' => 'Redefinir Password',
                'token' => $token
            ]);
            return;
        }
        
        // Valida a nova password
        $errors = [];
        
        if (empty($_POST['password'])) 
        {
            $errors['password'] = 'A password é obrigatória';
        } 
        elseif (strlen($_POST['password']) < 6) 
        {
            $errors['password'] = 'A password deve ter pelo menos 6 caracteres';
        }
        
        if ($_POST['password'] !== $_POST['confirm_password']) 
        {
            $errors['confirm_password'] = 'As passwords não coincidem';
        }
        
        if (!empty($errors)) 
        {
            $this->render('user/reset-password', [
                'pageTitle' => 'Redefinir Password',
                'token' => $token,
                'errors' => $errors
            ]);
            return;
        }
        
        // Atualiza a password
        if ($this->userModel->updatePassword($_POST['password'])) 
        {
            $this->render('user/reset-password-success', ['pageTitle' => 'Password Redefinida']);
        } 
        else 
        {
            $this->render('user/reset-password', [
                'pageTitle' => 'Redefinir Password',
                'token' => $token,
                'error' => 'Ocorreu um erro ao redefinir a password. Tenta novamente.'
            ]);
        }
    }
  
public function changePassword() 
{
    // Verifica se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) 
    {
        $this->redirect('/?controller=user&action=login');
        return;
    }
    
    // Se não for um pedido POST, mostra o formulário
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
    {
        $this->render('user/change-password', ['pageTitle' => 'Mudar Password']);
        return;
    }
    
    // Valida os dados
    $errors = [];
    
    if (empty($_POST['current_password'])) 
    {
        $errors['current_password'] = 'A password atual é obrigatória';
    }
    
    if (empty($_POST['new_password'])) 
    {
        $errors['new_password'] = 'A nova password é obrigatória';
    } 
    elseif (strlen($_POST['new_password']) < 6) 
    {
        $errors['new_password'] = 'A nova password deve ter pelo menos 6 caracteres';
    }
    
    if ($_POST['new_password'] !== $_POST['confirm_password']) 
    {
        $errors['confirm_password'] = 'As passwords não coincidem';
    }
    
    if (!empty($errors)) 
    {
        $this->render('user/change-password', [
            'pageTitle' => 'Mudar Password',
            'errors' => $errors
        ]);
        return;
    }
    
    // Carrega os dados do utilizador
    $this->userModel->getUserById($_SESSION['user_id']);
    
    // Tenta mudar a password
    if ($this->userModel->changePassword($_POST['current_password'], $_POST['new_password'])) 
    {
        // Password alterada com sucesso
        $this->render('user/change-password-success', ['pageTitle' => 'Password Alterada']);
    } 
    else 
    {
        // Falha ao alterar a password
        $this->render('user/change-password', [
            'pageTitle' => 'Mudar Password',
            'error' => 'Password atual incorreta'
        ]);
    }
}

// Página de perfil do utilizador
public function profile() 
{
    // Verifica se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) 
    {
        $this->redirect('/?controller=user&action=login');
        return;
    }
    
    // Carrega os dados do utilizador
    $this->userModel->getUserById($_SESSION['user_id']);
    
    // Se for um pedido POST, atualiza o perfil
    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Validação dos dados
        $errors = [];
        
        // Valida nome de utilizador
        if (empty($_POST['username'])) 
        {
            $errors['username'] = 'O nome de utilizador é obrigatório';
        } 
        elseif (strlen($_POST['username']) < 3) 
        {
            $errors['username'] = 'O nome de utilizador deve ter pelo menos 3 caracteres';
        }
        
        // Valida email
        if (empty($_POST['email'])) 
        {
            $errors['email'] = 'O email é obrigatório';
        } 
        elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        {
            $errors['email'] = 'Introduz um email válido';
        }
        
        // Verifica se existem erros
        if (!empty($errors)) 
        {
            $this->render('user/profile', [
                'pageTitle' => 'Perfil',
                'user' => $this->userModel,
                'errors' => $errors
            ]);
            return;
        }
        
        // Atualiza os dados do utilizador
        $oldUsername = $this->userModel->username;
        $oldEmail = $this->userModel->email;
        
        $this->userModel->username = $_POST['username'];
        $this->userModel->email = $_POST['email'];
        $this->userModel->notification_enabled = isset($_POST['notification_enabled']);
        
        // Verifica se o nome de utilizador ou email já existem
        if (($this->userModel->username !== $oldUsername || $this->userModel->email !== $oldEmail) 
            && $this->userModel->userExists()) 
        {
            $this->render('user/profile', [
                'pageTitle' => 'Perfil',
                'user' => $this->userModel,
                'error' => 'Este nome de utilizador ou email já está em uso'
            ]);
            return;
        }
        
        // Tenta atualizar o perfil
        if ($this->userModel->updateUser()) 
        {
            // Atualiza o nome de utilizador na sessão
            $_SESSION['username'] = $this->userModel->username;
            
            $this->render('user/profile', [
                'pageTitle' => 'Perfil',
                'user' => $this->userModel,
                'success' => 'Perfil atualizado com sucesso'
            ]);
        } 
        else 
        {
            $this->render('user/profile', [
                'pageTitle' => 'Perfil',
                'user' => $this->userModel,
                'error' => 'Ocorreu um erro ao atualizar o perfil'
            ]);
        }
        
        return;
    }
    
    // Mostra o formulário com os dados atuais
    $this->render('user/profile', [
        'pageTitle' => 'Perfil',
        'user' => $this->userModel
    ]);
}

}