UPLOAD_DIR=app/uploads

build:
	docker-compose build

init:
	mkdir -p $(UPLOAD_DIR)
	chmod -R 777 $(UPLOAD_DIR)

setup: build init

shell:
	docker exec -it camagru_web bash


up:
	docker-compose up

upd:
	docker-compose up -d

down:
	docker-compose down

clean:
	docker-compose down -v

reset: clean setup up
