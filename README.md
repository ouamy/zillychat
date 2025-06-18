# ZillyChat
A chat platform for the silly geese.

## Prerequisites
- Composer
- MySQL
- PhpMyAdmin
- Caddy

## Install
```bash
cp .env.example .env
php artisan key:generate
```
## Run
```bash
php artisan serve
php -S localhost:8080
```
## Deploy
```bash
cloudflared tunnel --url http://localhost:8000
cloudflared tunnel --url http://localhost:8080
```

