# Restaurant POS Phase 1 (Laravel 11 + Breeze) — Run with `php artisan serve`

This is a complete Laravel 11 project (no `vendor/` included).  
You will run `composer install` on your machine. You can point `.env` to **your own database**.

## Features
- Auth (Login/Register/Logout) — Breeze-style controllers + Blade views (Bootstrap CDN, no Vite required)
- Staff routes are **protected by auth**
- Dine-in: 12 tables, one open bill per table
- QR ordering per table (public): adds items directly to open bill
  - Customer cannot edit/delete after submit
  - No name/phone required
- Delivery (staff key-in): create delivery order + optional delivery details
- Discount (amount/percent)
- Checkout: split payments + change estimation

## Install
```bash
composer install
cp .env.example .env
php artisan key:generate
```

## Database
Edit `.env` to match your database (MySQL/Postgres/SQLite).

Then:
```bash
php artisan migrate
php artisan db:seed --class=\Database\Seeders\Phase1Seeder
php artisan db:seed --class=\Database\Seeders\AuthSeeder
```

Default login created by AuthSeeder:
- Email: `admin@example.com`
- Password: `password`

Change after first login.

## Run
```bash
php artisan serve
```

## URLs
- Login: `/login`
- Staff dashboard: `/staff/orders`
- QR: Each table card shows `/t/{public_uuid}`

## Notes
- This project intentionally uses **Bootstrap CDN** for speed; no Node/Vite is required to run.
- If you later want Tailwind/Vite, you can install Breeze frontend assets normally.


## Product Images
Run once:

php artisan storage:link

Then you can upload images in Staff > Products.
