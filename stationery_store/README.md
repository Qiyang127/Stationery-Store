# Paper & Pen - Stationery Store

A simple PHP + MySQL stationery store demo with products, cart, wishlist, contact form, and user auth.

## Requirements
- PHP 8.1+
- MySQL 8+

## Setup (Local without Docker)
1. Create database and import schema:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS stationery_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p stationery_store < database/schema.sql
```
2. Configure DB credentials in `config.php`.
3. Serve the `public/` directory with PHP built-in server for local dev:
```bash
php -S 0.0.0.0:8000 -t public
```
Open http://localhost:8000

## Setup (Docker)
```bash
docker compose up --build -d
```
- App: http://localhost:8000
- phpMyAdmin: http://localhost:8080 (server: db, user: root, pass: rootpass)

## Notes
- Login/Register uses sessions and password hashing. Wishlist is user-scoped in DB. Cart is session-based.
- CSRF protection is implemented for all POST actions.
- UI uses Bootstrap 5 and Bootstrap Icons.