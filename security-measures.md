# Medidas de Segurança Implementadas no Camagru

Este documento detalha as medidas de segurança implementadas no projeto Camagru para proteger os dados dos utilizadores e prevenir vulnerabilidades comuns em aplicações web.

## Proteção de Dados

### Armazenamento Seguro de Palavras-passe

As palavras-passe dos utilizadores são protegidas utilizando técnicas modernas de hash:

```php
// Trecho do método register() no modelo User
$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
```

- Utilizamos a função `password_hash()` nativa do PHP com o algoritmo BCRYPT
- O BCRYPT gera automaticamente um salt único para cada palavra-passe
- O custo padrão é 10, o que significa 2^10 iterações do algoritmo
- Este método oferece proteção contra ataques de força bruta, dicionário e rainbow tables
- Mesmo que a base de dados seja comprometida, é computacionalmente inviável recuperar as palavras-passe originais

Exemplo de verificação durante o login:

```php
// Trecho do método login() no modelo User
if (password_verify($plainPassword, $this->password_hash)) {
    // Login bem-sucedido
}
```

### Verificação por Email

Para confirmar a identidade dos utilizadores e prevenir registos maliciosos:

```php
// Geração de token único durante o registo
$this->verification_token = bin2hex(random_bytes(50));

// Envio de email de verificação
$subject = "Verificação de Conta - Camagru";
$verify_link = "http://localhost:8000/?controller=user&action=verify&token=$token";
mail($email, $subject, $message, $headers);
```

- Cada novo utilizador recebe um token único de 100 caracteres hexadecimais
- O token é armazenado na base de dados e enviado por email
- A conta permanece inativa até à verificação
- Após verificação, o token é eliminado da base de dados
- Este processo garante que o email fornecido é válido e pertence ao utilizador

### Tokens Temporários para Recuperação de Palavra-passe

Para uma recuperação segura:

```php
// Geração de token com prazo de validade
$this->reset_token = bin2hex(random_bytes(50));
$this->reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Verificação de validade do token
$query = "SELECT id FROM " . $this->table . " 
          WHERE reset_token = :token AND reset_token_expiry > NOW()";
```

- Tokens de 100 caracteres hexadecimais gerados criptograficamente
- Cada token tem uma validade de apenas 1 hora
- O token é verificado tanto pela correspondência quanto pela validade temporal
- Após redefinição da palavra-passe, o token é invalidado
- Este mecanismo previne ataques de força bruta e restringe a janela de vulnerabilidade

## Prevenção contra Injeção SQL

A injeção SQL é uma das vulnerabilidades mais comuns e perigosas em aplicações web. Implementámos várias camadas de proteção:

### Utilização de PDO com Prepared Statements

Toda a interação com a base de dados utiliza PDO (PHP Data Objects) com prepared statements:

```php
// Exemplo de query segura para login
$query = "SELECT id, username, email, password, verified FROM " . $this->table . " 
          WHERE username = :username OR email = :email";

$stmt = $this->db->prepare($query);
$stmt->bindParam(":username", $this->username);
$stmt->bindParam(":email", $this->email);
$stmt->execute();
```

- O PDO separa completamente o SQL dos dados
- As queries são preparadas pelo servidor de base de dados antes da inserção de valores
- Isto garante que os dados do utilizador nunca são interpretados como código SQL

### Parâmetros Vinculados (Binding)

Todos os dados externos são vinculados como parâmetros:

```php
// Exemplo de inserção segura de comentário
$query = "INSERT INTO comments (image_id, user_id, comment) 
          VALUES (:image_id, :user_id, :comment)";

$stmt = $this->db->prepare($query);
$stmt->bindParam(':image_id', $image_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':comment', $comment);
$stmt->execute();
```

- Nunca concatenamos diretamente valores nas queries SQL
- Cada parâmetro é vinculado individualmente com tipo apropriado
- O PDO lida com o escape de caracteres especiais automaticamente
- Utilizamos `bindParam()` em vez de `bindValue()` para maior segurança

### Validação Adicional

Além das prepared statements, implementamos validações adicionais:

```php
// Validação de ID numérico
$image_id = (int)$_GET['id'];  // Conversão forçada para inteiro

// Validação de entrada de texto
if (empty($_POST['comment']) || strlen($_POST['comment']) > 500) {
    // Rejeita comentários vazios ou muito longos
}
```

- Conversão de tipos para garantir que IDs são sempre numéricos
- Validação de comprimento para prevenir ataques de negação de serviço
- Verificação de valores vazios ou inválidos antes de interagir com a base de dados

## Prevenção contra XSS (Cross-Site Scripting)

O XSS permite a injeção de scripts maliciosos que podem roubar cookies de sessão ou executar ações não autorizadas.

### Sanitização de Dados Introduzidos

Todos os dados recebidos são sanitizados antes de processamento:

```php
// Sanitização básica
$this->username = htmlspecialchars(strip_tags($this->username));
$this->email = htmlspecialchars(strip_tags($this->email));

// Sanitização antes de inserir na base de dados
$comment = htmlspecialchars($_POST['comment']);
```

- A função `strip_tags()` remove todas as tags HTML e PHP
- A função `htmlspecialchars()` converte caracteres especiais em entidades HTML
- Isto previne que qualquer código injetado seja executado pelo navegador

### Escape de Saída

Todos os dados exibidos são sempre escapados:

```php
<!-- Exemplo de escape na saída -->
<p><?= htmlspecialchars($image['username']) ?></p>
<p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
```

- Mesmo que os dados já estejam sanitizados na base de dados, aplicamos escape na saída
- Esta dupla proteção garante que nenhum código malicioso seja renderizado
- A função `nl2br()` preserva quebras de linha sem permitir HTML

### Validação no Cliente e Servidor

Implementamos validação tanto no cliente quanto no servidor:

```javascript
// Validação no cliente
if (password.length < 6) {
    showError('A palavra-passe deve ter pelo menos 6 caracteres');
    return false;
}
```

```php
// Validação no servidor (mesmo com validação no cliente)
if (strlen($_POST['password']) < 6) {
    $errors['password'] = 'A palavra-passe deve ter pelo menos 6 caracteres';
}
```

- A validação no cliente proporciona feedback imediato
- A validação no servidor é incontornável e garante segurança mesmo se a validação do cliente for burlada
- Todas as entradas são validadas quanto a tipo, comprimento e formato

### Headers de Segurança

Adicionalmente, configuramos headers HTTP de segurança:

```php
// Proteção contra clickjacking
header('X-Frame-Options: DENY');

// Proteção contra MIME-sniffing
header('X-Content-Type-Options: nosniff');

// Política de Conteúdo Seguro (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```

Estes headers ajudam a prevenir ataques XSS e outros vetores de ataque relacionados.

## Proteção de Ficheiros Carregados

### Validação de Tipo de Ficheiro

```php
// Verificação do tipo de ficheiro
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowedTypes)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Tipo de ficheiro não permitido']);
    exit;
}
```

### Geração de Nomes Únicos

```php
// Gera um nome único para o ficheiro
$filename = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
```

### Verificação de Imagem Real

```php
// Verifica se é uma imagem válida
if (!getimagesize($file['tmp_name'])) {
    // Rejeita o ficheiro se não for uma imagem válida
}
```

## Conclusão

A segurança da aplicação Camagru foi uma prioridade durante todo o desenvolvimento. Através da implementação de múltiplas camadas de proteção, prevenimos as vulnerabilidades mais comuns em aplicações web, criando um ambiente seguro para os utilizadores partilharem e interagirem com imagens.

Estas medidas de segurança estão alinhadas com as melhores práticas atuais e garantem que os dados dos utilizadores estão protegidos contra acessos não autorizados e manipulações maliciosas.
