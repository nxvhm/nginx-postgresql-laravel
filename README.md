The repository contains very basic docker setup aimed to quickly bootstrap a development environment for a new Laravel project using PostgreSQL as database.

Some of the scripts used in the repository were copied from [Ric Harvey's nginx-php-fpm repository](https://github.com/richarvey/nginx-php-fpm)

## Installation

1. Clone the repository on your file system. E.g: `/my/project/dir`

2. Navigate to that directory and create the source code folder
```sh
cd /my/project/dir
mkdir src
```
>! When working on existing project just clone the source code inside the src/ folder and then run `docker-compose`

2. Run:
```sh
docker-compose up -d
```
3. Create New Laravel Project

```sh
cd /my/project/dir
docker-compose exec -i nginx-php bash -c "composer create-project laravel/laravel ."
```
4. Copy the `.env` file and set the app keys and run the initial migrations
```sh
docker-compose cp nginx-php/app.env.example nginx-php:/var/www/html/.env
docker-compose exec -i nginx-php bash -c "php artisan key:generate"
docker-compose exec -i nginx-php bash -c "php artisan migrate"
```

## Visibility

You can view the project from `http://localhost:5500` or `https://localhost:5543`. To change the ports edit docker-compose file. 
Your postgreSQL is available at `127.0.0.1:54320`.


### Refresh Self Signed SSL

To regenerate the certificates run:
```sh
openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout cert/mycert.key -out cert/mycert.crt
```
