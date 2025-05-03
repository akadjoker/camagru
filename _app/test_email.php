<?php
 

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Teste de Email com MailHog</h1>";

// Mostra as configurações atuais
echo "<h2>Configuração PHP</h2>";
echo "<pre>";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "</pre>";

// Tenta enviar um email
$to = "djokerstreams@gmail.com";
$subject = "Teste do Camagru - " . date('H:i:s');
$message = "
<html>
<head>
    <title>Teste de Email</title>
</head>
<body>
    <h2>Email de teste do Camagru</h2>
    <p>Este é um email de teste enviado em: " . date('Y-m-d H:i:s') . "</p>
</body>
</html>
";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: djokertheripper@gmail.com" . "\r\n";

echo "<h2>Tentando enviar email</h2>";
$result = mail($to, $subject, $message, $headers);

echo "Resultado: " . ($result ? "Sucesso" : "Falha") . "<br>";
echo "Verifica a interface do MailHog em: <a href='http://localhost:8025' target='_blank'>http://localhost:8025</a>";