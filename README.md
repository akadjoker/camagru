# Documentação do Projeto Camagru

## Resumo do Projeto

Camagru é uma aplicação web que permite aos utilizadores capturar fotos com a webcam ou fazer upload de imagens, aplicar sobreposições e filtros, e partilhá-las numa galeria pública onde outros utilizadores podem dar like e comentar.

## Começar o Projeto

### Requisitos
- Docker e Docker Compose
- Git

### Passos Iniciais

1. **Clonar o repositório**
   ```bash
   git clone https://github.com/akadjoker/camagru.git
   cd camagru
   ```

2. **Configurar o ficheiro .env**
   Cria um ficheiro `.env` na raiz do projeto com as seguintes variáveis:
   ```
   # PostgreSQL
   POSTGRES_DB=camagru
   POSTGRES_USER=teu_utilizador
   POSTGRES_PASSWORD=tua_palavra_passe

   # Configuração PHP
   DB_HOST=db
   DB_PORT=5432
   DB_NAME=camagru
   DB_USER=teu_utilizador
   DB_PASSWORD=tua_palavra_passe

   # pgAdmin
   PGADMIN_DEFAULT_EMAIL=teu_email@exemplo.com
   PGADMIN_DEFAULT_PASSWORD=tua_palavra_passe_pgadmin
   ```

3. **Iniciar os contentores Docker**
   ```bash
   docker-compose up -d
   ```

4. **Verificar se os contentores estão em execução**
   ```bash
   docker-compose ps
   ```

   Deves ver três serviços em execução: web, db, pgadmin

5. **Aceder à aplicação**
   Abre o navegador e acede a: `http://localhost:8000`

### Principais Comandos Docker

- **Iniciar os contentores**
  ```bash
  docker-compose up -d
  ```

- **Parar os contentores**
  ```bash
  docker-compose down
  ```

- **Visualizar registos**
  ```bash
  docker-compose logs
  docker-compose logs web  # Apenas do servidor web
  docker-compose logs db   # Apenas da base de dados
  ```

- **Aceder à consola dentro do contentor**
  ```bash
  docker-compose exec web bash
  ```

- **Reconstruir contentores (após alterações no Dockerfile)**
  ```bash
  docker-compose up --build -d
  ```

## Configuração do pgAdmin

1. Acede ao pgAdmin no navegador: `http://localhost:8888`
2. Faz login com as credenciais definidas no ficheiro `.env` (PGADMIN_DEFAULT_EMAIL e PGADMIN_DEFAULT_PASSWORD)
3. Adiciona um novo servidor:
   - Em "General", preenche "Name" com "Camagru"
   - Em "Connection", preenche:
     - Host: `db` (nome do serviço no docker-compose)
     - Port: `5432`
     - Maintenance Database: `camagru` (ou o que definiste em POSTGRES_DB)
     - Username: O valor definido em POSTGRES_USER
     - Password: O valor definido em POSTGRES_PASSWORD
   - Marca a opção "Save Password"
4. Clica em "Save"

Após a conexão, podes gerir a base de dados, executar queries e verificar as tabelas criadas.

## Testar o Sistema de Email

1. Acede ao MailHog no navegador: `http://localhost:8025`
2. A interface do MailHog mostrará todos os emails enviados pela aplicação
3. Para testar o envio de emails:
   - Regista um novo utilizador
   - Verifica no MailHog se recebeste o email de confirmação
   - Clica no link de confirmação para verificar a conta
   - Experimenta também a funcionalidade "Esqueci a palavra-passe"

O MailHog captura todos os emails em ambiente de desenvolvimento, evitando o envio real.

## Arquitetura

O projeto segue a arquitetura MVC (Model-View-Controller) para melhor organização do código e separação de responsabilidades:

- **Models**: Gerem a interação com a base de dados e a lógica de negócio
- **Views**: Apresentam a interface ao utilizador
- **Controllers**: Controlam o fluxo da aplicação e fazem a ligação entre Models e Views

## Tecnologias Utilizadas

### Back-end
- **PHP**: Linguagem de programação para o servidor
- **PostgreSQL**: Sistema de gestão de base de dados relacional
- **PDO**: Para interação segura com a base de dados

### Front-end
- **HTML5/CSS3**: Para estrutura e estilo das páginas
- **JavaScript**: Para interatividade e manipulação da webcam
- **Bulma**: Framework CSS para design responsivo

### Infraestrutura
- **Docker**: Para contentorização da aplicação
- **MailHog**: Para teste de envio de emails em ambiente de desenvolvimento

## Funcionalidades Implementadas

### Sistema de Autenticação
- Registo de utilizadores com validação por email
- Login/logout
- Recuperação de palavra-passe via email
- Modificação de perfil (nome de utilizador, email, palavra-passe)
- Configuração de preferências de notificação

### Editor de Imagens
- Captura de fotos com webcam
- Upload de imagens
- Aplicação de sobreposições (molduras, objetos)
- Aplicação de filtros (preto e branco, sépia, etc.)
- Processamento de imagens no servidor (GD Library)

### Galeria
- Visualização de imagens de todos os utilizadores
- Paginação das imagens
- Sistema de likes
- Sistema de comentários
- Notificações por email para novos comentários

## Medidas de Segurança

### Proteção de Dados
- **Palavras-passe**: Armazenadas com hash seguro (password_hash com BCRYPT)
- **Verificação por email**: Para confirmar a identidade do utilizador
- **Tokens temporários**: Para recuperação de palavra-passe

### Prevenção contra Injeção SQL
- Utilização do PDO com prepared statements
- Parâmetros sempre vinculados (bindParam), nunca concatenados

### Prevenção contra XSS (Cross-Site Scripting)
- Sanitização de dados introduzidos pelos utilizadores
- Utilização de htmlspecialchars() para escape de caracteres especiais
- Validação de dados tanto no cliente como no servidor

### Proteção de Ficheiros
- Verificação do tipo de ficheiros permitidos
- Geração de nomes únicos para evitar sobreposição
- Armazenamento fora do diretório público

### Segurança de Sessão
- Validação de sessão em todas as operações restritas
- Regeneração de ID de sessão após login

### Segurança Detalhada 
   security-measures.md

## Sistema de Email

A aplicação utiliza emails para várias funcionalidades importantes:

1. **Verificação de conta**: Após o registo, é enviado um email com um link único para verificação
2. **Recuperação de palavra-passe**: Envio de token temporário para redefinição segura
3. **Notificações de comentários**: Notificação aos donos das imagens quando recebem novos comentários

Em ambiente de desenvolvimento, utilizamos o MailHog, que:
- Captura todos os emails enviados pela aplicação
- Fornece uma interface web para visualizar estes emails
- Não envia realmente os emails para os destinatários reais

Em ambiente de produção, seria substituído por um servidor SMTP real.

## Funções Principais

### Funções de Autenticação
- `register()`: Registo de novo utilizador
- `verifyAccount()`: Verificação de conta através de token
- `login()`: Autenticação de utilizador
- `forgotPassword()`: Solicitação de recuperação de palavra-passe
- `resetPassword()`: Redefinição de palavra-passe com token

### Funções de Manipulação de Imagem
- `applyOverlay()`: Aplica uma sobreposição a uma imagem base
- `applyFilter()`: Aplica filtros visuais (preto e branco, sépia, etc.)
- `saveImage()`: Guarda a imagem processada na base de dados

### Funções de Interação Social
- `toggleLike()`: Adiciona ou remove um like
- `addComment()`: Adiciona um comentário a uma imagem
- `notifyImageOwner()`: Envia notificação de comentário ao dono da imagem

## Interface do Utilizador

A interface do utilizador é responsiva e foi desenhada para proporcionar uma experiência consistente em dispositivos móveis e desktop. Inclui:

- **Modais personalizados** para mensagens e confirmações
- **Formulários validados** com feedback visual
- **Galeria de imagens** com sistema de paginação
- **Editor intuitivo** para captura e modificação de imagens

## Conclusão

O projeto Camagru implementa todas as funcionalidades obrigatórias especificadas, com foco na segurança, usabilidade e responsividade. A arquitetura MVC proporciona uma base sólida para o desenvolvimento, e as tecnologias escolhidas garantem um bom desempenho e facilidade de manutenção.
