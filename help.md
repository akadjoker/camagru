docker-compose up -d

docker-compose ps

docker-compose logs camagru_web

docker-compose exec camagru_web bash
php config/setup.php

docker-compose down

phpadmin
      http://localhost:8888 
 

Email:   PGADMIN_DEFAULT_EMAIL
Password:  PGADMIN_DEFAULT_PASSWORD


 
Separador "General":

Name: "Camagru"  


Separador "Connection":

Host name/address: "db"  
Port: "5432"
Maintenance database: "postgres" 
Username:   POSTGRES_USER
Password:  POSTGRES_PASSWORD
Save password: Marcar esta opção se quiseres guardar a password

MailHog em: http://localhost:8025


limpar as tbales e criar novas

Parar os containers: docker-compose down
Remover o volume: docker volume rm camagru_db_data
Iniciar novamente: docker-compose up -d

https://www.stickpng.com/img/download/580b585b2edbce24c47b2408
