# Customer Area

Customer Area is a Laravel customer portal and license management backend for software distribution.

The application serves three audiences:

- Admin users under `/admin/*`
- Customer users under `/user/*`
- Desktop installers under `/api/*`

## Local Setup

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
php artisan serve
```

## Bot Protection (Google reCAPTCHA)

The login page uses Google reCAPTCHA v2 (checkbox) when enabled.

Add these to your `.env`:

```bash
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

For this Windows/XAMPP workspace, use:

```bash
C:\xampp\php\php.exe artisan migrate:fresh --seed
cmd /c npm run dev
C:\xampp\php\php.exe artisan serve
```

## Seeded Accounts

- `admin@example.com` / `password`
- `admin.ops@example.com` / `password`
- `user@example.com` / `password`
- `ops@acme.example` / `password`
- `finance@acme.example` / `password`
- `dispatch@nusantara.example` / `password`
- `qa@sagara.example` / `password`
- `command@metro-gov.example` / `password`

`php artisan db:seed` creates a broad local demo dataset: organizations, users,
product trees, license types, licenses, activations, entitlements, downloads,
and download logs. The seeder is idempotent, so it can be rerun without
duplicating the main demo records.

## Architecture Notes

- Product selector trees are built through `App\Support\ProductTreeBuilder` so
  admin product, license, entitlement, and download forms share one hierarchy
  formatter.
- Product tree rendering only builds search/filter metadata for interactive
  trees. Detail-page child trees pass catalog paths down during recursion to
  avoid repeated parent-chain path lookups.
- License activation status checks should use model constants such as
  `LicenseActivation::STATUS_ACTIVE` instead of raw status strings.

## Verification

```bash
C:\xampp\php\php.exe artisan test
npm.cmd run build
```

## Day 1 Baseline

- Laravel 12
- Laravel Breeze with Blade
- Tailwind CSS and Vite
- SQLite local database
- Database-backed sessions, cache, and queue tables
- Project folders for admin, user, and API controllers
- Private downloads directory at `storage/app/private/downloads`

Do not regenerate `APP_KEY` after real encrypted license data exists.
