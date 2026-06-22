# Week 2 Acceptance Test Report

Date: 2026-05-07

Branch: `feature/week-2-licensing-delivery`

## Commands Run

```powershell
C:\xampp\php\php.exe artisan test tests\Feature\DayEightProductTreeTest.php tests\Feature\DayNineProductTreeUxTest.php tests\Feature\DayTenLicenseTypesAndLicenseModelTest.php tests\Feature\DayElevenAdminLicenseManagementTest.php tests\Feature\DayTwelveBatchLicensesAndActivationSupportTest.php tests\Feature\DayThirteenEntitlementsAndDownloadItemsTest.php tests\Feature\DayFourteenCustomerPortalTest.php
C:\xampp\php\php.exe artisan test
cmd /c npm run build
C:\xampp\php\php.exe artisan route:list --except-vendor
```

## Results

```text
Technical deliverable path check passed
Week 2 focused tests: 35 passed, 351 assertions
Week 2 handoff workflow test: 1 passed, 26 assertions
Full test suite: 106 passed, 742 assertions
Vite build passed
Registered routes: 93
```

## Acceptance Checklist Status

| Area | Status |
| --- | --- |
| Product tree | PASS |
| License types | PASS |
| Licenses | PASS |
| Activations and support | PASS |
| Entitlements | PASS |
| Downloads | PASS |
| Customer portal | PASS |

## Verified Functionality

- Product tree supports top-level products, nested products, more than two levels of nesting, recursive tree views, breadcrumbs, inactive styling, and backend cycle prevention.
- License types are database-backed, seeded, managed by admin CRUD, and loaded dynamically by license forms.
- Licenses can be issued to users with product/sub-product selection, quantity, max activations, expiry date, generated keys, encrypted storage, hash lookup, masked display, protected reveal, and expiry helpers.
- License activation support includes activation rows, reset-all support action, and individual activation deletion.
- Entitlements can be granted with date windows and active/expired/suspended status, with duplicate user/product pairs blocked.
- Download items attach to products, can be user-specific, support version/expiry/status/path/size metadata, and use private storage.
- Customer portal shows scoped products, licenses, expiry warnings, downloads, and download history.
- Customer downloads check entitlement, download window, item status, item expiry, user-specific ownership, private file existence, and write a `download_logs` record before streaming.
- The full Week 2 browser handoff workflow is covered by `tests/Feature/WeekTwoHandoffWorkflowTest.php`.

## Manual Browser Checks Still Recommended

- Product tree indentation and breadcrumbs at three or more levels.
- License reveal modal/action UX.
- Batch license creation form UX.
- Activation reset confirmation wording.
- Download upload/register form using a real installer-like file.
- Customer private download from the browser and matching `download_logs` row.

## Notes

- `php artisan migrate:fresh --seed` was not run during this acceptance pass to avoid resetting local development data. Automated tests use fresh test databases and passed.
- The public installer API remains placeholder-level for Week 3.
- Do not regenerate `APP_KEY` after real license data exists because encrypted license keys depend on it.
