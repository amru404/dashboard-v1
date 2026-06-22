# Week 1 Handoff

Customer Area now has a stable Laravel foundation for the admin and customer portal.

## Completed

- Laravel 12 application with Breeze Blade authentication.
- Tailwind CSS and Vite frontend pipeline.
- SQLite local development database.
- Database-backed sessions, cache, and queues configured by Laravel defaults.
- Week 1 folder structure for admin controllers, user controllers, API controllers, views, middleware, models, seeders, and private downloads.
- Full database skeleton for organizations, users, products, license types, licenses, license activations, entitlements, download items, and download logs.
- Seeded system and customer organizations.
- Seeded admin and customer users.
- Role-aware dashboard redirect.
- Protected admin and customer route groups.
- Inactive-account logout enforcement.
- Server-verified slider CAPTCHA on login.
- Branded green login page and shared admin UI components.
- Admin organization CRUD with safe deletion behavior.
- Admin user CRUD with filters, organization assignment, password handling, and self-lockout protection.

## Local Run Commands

From the project directory:

```powershell
cd C:\Users\PREDATOR\WebstormProjects\untitled\customer-area
C:\xampp\php\php.exe artisan migrate:fresh --seed
cmd /c npm run dev
C:\xampp\php\php.exe artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Seeded Credentials

```text
admin@example.com / password
user@example.com / password
```

## Verification

Current Week 1 verification:

```powershell
C:\xampp\php\php.exe artisan migrate:fresh --seed
C:\xampp\php\php.exe artisan test
cmd /c npm run build
```

Expected result:

```text
65 tests passing
Vite build succeeds
```

## Known Limitations

- The slider CAPTCHA is intentionally simple and has a TODO to replace it with a production-grade bot defense before public deployment.
- Product, license, entitlement, download, and installer API business workflows are not implemented yet.
- Download files must remain in private storage and should later be served only through authenticated controller actions.
- License keys must not be encrypted until APP_KEY handling is locked down for production. Do not regenerate APP_KEY after real license data exists.

## Week 2 Scope

- Product tree management with unlimited nesting through `products.parent_id`.
- License type management.
- License management.
- Entitlement assignment.
- Private download item management.
- Customer portal pages for owned products, licenses, and downloads.
