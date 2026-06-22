# Full Regression And Optimization Test Report

Date: 2026-05-08

Branch: `feature/week-2-licensing-delivery`

## Commands Run

```powershell
# PowerShell Test-Path check for the 32 deliverable/support paths in the checklist
C:\xampp\php\php.exe artisan test tests\Feature\DayEightProductTreeTest.php tests\Feature\DayNineProductTreeUxTest.php tests\Feature\DayTenLicenseTypesAndLicenseModelTest.php tests\Feature\DayElevenAdminLicenseManagementTest.php tests\Feature\DayTwelveBatchLicensesAndActivationSupportTest.php tests\Feature\DayThirteenEntitlementsAndDownloadItemsTest.php tests\Feature\DayFourteenCustomerPortalTest.php tests\Feature\WeekTwoHandoffWorkflowTest.php
C:\xampp\php\php.exe artisan test
npm.cmd run build
C:\xampp\php\php.exe artisan route:list --except-vendor
git status --short --branch
```

## Results

```text
Deliverable and support path check: PASS
Paths checked: 32
Paths found: 32
Missing paths: none

Focused Week 2 plus handoff tests: PASS
Tests: 36 passed
Assertions: 386 passed
Duration: 1.82 seconds

Full PHP test suite: PASS
Tests: 109 passed
Assertions: 777 passed
Duration: 5.70 seconds

Vite production build: PASS
Modules transformed: 55
CSS bundle: public/build/assets/app-BWJIotxC.css, 47.17 kB
JS bundle: public/build/assets/app-mFDbMUqZ.js, 89.47 kB
Build duration: 1.36 seconds

Route registration: PASS
Non-vendor routes registered: 93
```

## Passing Test Areas

| Area | Result |
| --- | --- |
| Unit baseline | PASS |
| Authentication and Breeze flows | PASS |
| Slider CAPTCHA and branded login | PASS |
| Role routing and inactive-user enforcement | PASS |
| Organization management | PASS |
| User management | PASS |
| Database foundation and seed baseline | PASS |
| Product tree model and admin UI | PASS |
| Product breadcrumbs and tree safety rules | PASS |
| License types and license model | PASS |
| Admin license management | PASS |
| Batch licenses and activation support | PASS |
| Entitlements and download items | PASS |
| Customer portal products, licenses, and downloads | PASS |
| Week 2 handoff workflow | PASS |
| Week 2 technical deliverable and optimization support path check | PASS |
| License activation and encryption edge cases | PASS |
| Product tree builder reuse regression | PASS |

## Specific High-Value Checks Covered

- License keys are encrypted at rest and stored with SHA-256 hashes for lookup.
- Admin plaintext reveal works only through protected endpoints.
- Product nesting supports recursive children and blocks circular parent assignments.
- Duplicate user/product entitlements are blocked.
- Customer portal queries are scoped to the authenticated customer.
- Customer downloads require active entitlement, valid download window, active/unexpired file, matching user restriction, private file existence, and audit logging.
- The full handoff workflow creates a product tree, license type, customer license, entitlement, private download item, customer views, private controller download, and `download_logs` row.
- Activation-limit helpers count active rows only, allow same-device idempotent checks, block new devices at the cap, handle unlimited licenses, and reject expired licenses.
- Corrupt encrypted license payloads fail closed and encrypted license values are never stored as plaintext.
- `App\Support\ProductTreeBuilder` is present as the shared option-tree builder used by the optimized admin forms.
- Product tree UX regressions remain covered after the count-only child badge and indentation connector styling changes.

## Not Run

```text
php artisan migrate:fresh --seed
```

This was intentionally skipped because it would delete current local development/demo data. The automated test suite uses fresh test databases and passed.

## Notes

- Checks were rerun with PowerShell profile loading disabled so the recorded command output is clean.
- Manual browser review is still recommended for product tree indentation connectors, visual layout, reveal modal behavior, batch license UX, activation reset confirmation, upload/register forms, and real private download UX.
