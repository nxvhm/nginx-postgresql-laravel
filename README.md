Some of the scripts used in the repository was copied from [Ric Harvey's nginx-php-fpm repository](https://github.com/richarvey/nginx-php-fpm)

# Installation

Run:
```sh
docker-compose up -d
```
# Create Empty Laravel Project

```sh
cd /my/project/dir
docker-compose exec -i nginx-php bash -c "composer create-project laravel/laravel ."
docker-compose cp nginx-php/app.env.example nginx-php:/var/www/html/.env
docker-compose exec -i nginx-php bash -c "php artisan key:generate"
```
