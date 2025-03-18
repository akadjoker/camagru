<?php
// Inclui o autoloader do Composer
require_once ROOT . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer 
{
    private $mail;
    
    public function __construct() 
    {
        $this->mail = new PHPMailer(true);
        
        // Configurações do servidor
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'djoker_conta@gmail.com';   
        $this->mail->Password   = 'djoker_senha_de_app';     
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->CharSet    = 'UTF-8';
    }
    
    public function send($to, $subject, $body) 
    {
        try 
        {
            // Destinatários
            $this->mail->setFrom('djoker_conta@gmail.com', 'Camagru');
            $this->mail->addAddress($to);
            
            // Conteúdo
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $this->mail->send();
            return true;
        } 
        catch (Exception $e) 
        {
            error_log("Erro ao enviar email: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}