# ZillyChat
A chat platform for the silly geese.

## Prerequisites
- Composer
- MySQL
- phpMyAdmin

## Install
```bash
composer install
composer update
cp .env.example .env
php artisan key:generate
npm install
npm run build
```
## Run
```bash
php artisan serve
cd /usr/share/phpmyadmin
php -S localhost:8080
```
## Deploy
```bash
cloudflared tunnel --url http://localhost:8000
cloudflared tunnel --url http://localhost:8080
```

