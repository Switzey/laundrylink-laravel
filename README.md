# LaundryLink

LaundryLink is a Laravel web app that connects laundry and dry-cleaning businesses with clients. Clients can browse approved vendors, schedule pickup and delivery, create orders, track order activity, and leave reviews. Vendors can manage profiles, services, orders, schedules, and reports. Admins can approve vendors, monitor logistics, moderate reviews, and review platform reports.

Online payment is currently disabled. Orders keep a payment status for admin/vendor reporting, but payments are handled manually or offline.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite for local development
- External MySQL/PostgreSQL database for Vercel production

## Local Installation

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

Open `http://127.0.0.1:8000`.

## Default Login Accounts

All seeded accounts use the password `password`.

- Admin: `admin@example.com`
- Client: `customer@example.com`
- Vendor: `cleaner@example.com`

Additional seeded users and vendors are included for marketplace, reporting, review, and logistics demos.

## Useful Local URLs

- Landing page: `/`
- Cleaner marketplace: `/cleaners`
- Client dashboard: `/client/dashboard`
- Legacy customer dashboard: `/customer/dashboard`
- Customer address book: `/customer/addresses`
- New order: `/orders/create`
- Vendor dashboard: `/vendor/dashboard`
- Legacy cleaner dashboard: `/cleaner/dashboard`
- Vendor schedule: `/vendor/schedule`
- Vendor reports: `/vendor/reports`
- Admin dashboard: `/admin/dashboard`
- Admin logistics: `/admin/logistics`
- Admin reports: `/admin/reports`
- Admin review moderation: `/admin/reviews`
- Notifications: `/notifications`

## Vercel Deployment

This repo includes:

- `vercel.json` for Vercel routing and the community PHP runtime
- `api/index.php` as the Laravel serverless entrypoint
- `.vercelignore` to keep local/vendor/runtime files out of deployments

Set these environment variables in Vercel:

```env
APP_NAME=LaundryLink
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-vercel-domain.vercel.app
APP_STORAGE_PATH=/tmp/laundrylink-storage

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

Generate `APP_KEY` locally with:

```bash
php artisan key:generate --show
```

SQLite is fine locally, but do not rely on SQLite for Vercel production because serverless filesystems are temporary. Use a hosted MySQL or PostgreSQL database, then run migrations against that database:

```bash
php artisan migrate --seed --force
```

## Development Commands

```bash
php artisan migrate --seed
php artisan test
npm run build
```

Use `php artisan migrate:fresh --seed` only when you intentionally want to reset the local SQLite database.

## Notes

- Public cleaner listings only show approved and available vendors.
- Clients can only view and manage their own orders and addresses.
- Vendors can only manage their own profile, services, orders, schedule, and reports.
- Admin pages are protected by the admin role middleware.
- Order prices are calculated server-side from the services table.
- Online payment gateway routes have been removed for now.
