# LaundryLink

LaundryLink is a Laravel MVP that connects laundry and dry-cleaning businesses with customers. Stage 1 includes the public marketplace, order creation UI, customer dashboard, cleaner dashboard, admin dashboard, SQLite migrations, Eloquent relationships, and seeded demo data.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Then open `http://127.0.0.1:8000`.

## Demo accounts

All seeded users use the password `password`.

- Admin: `admin@laundrylink.test`
- Customer: `tola@example.com`
- Customer: `kemi@example.com`
- Cleaner: `bisi@freshfold.test`

Authentication is not implemented in Stage 1; the dashboards are open scaffold routes for product review.

## Stage 1 routes

- `/`
- `/customer/dashboard`
- `/cleaners`
- `/cleaners/{cleaner}`
- `/orders/create`
- `/cleaner/dashboard`
- `/admin/dashboard`

## Verification

```bash
php artisan test
npm run build
```
