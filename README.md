The repository contains very basic docker setup aimed to quickly bootstrap a development environment for a new Laravel project using PostgreSQL as database.

Some of the scripts used in the repository were copied from [Ric Harvey's nginx-php-fpm repository](https://github.com/richarvey/nginx-php-fpm)

# Installation

1. Clone the repository on your file system. E.g: `/my/project/dir`

2. Navigate to the directory and run `docker-compose up -d`:
```sh
cd /my/project/dir
docker-compose up -d
```
3. Create New Laravel Project

```sh
cd /my/project/dir
docker-compose exec -i nginx-php bash -c "composer create-project laravel/laravel ."
```
4. Copy the `.env` file and set the app keys
```sh
docker-compose cp nginx-php/app.env.example nginx-php:/var/www/html/.env
docker-compose exec -i nginx-php bash -c "php artisan key:generate"
```

