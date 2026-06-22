# Week 2 Progress Checklist

Branch: `feature/week-2-licensing-delivery`

Last updated: 2026-05-08

## Overall Week 2 Objective

Turn Customer Area from a protected admin/customer shell into the licensing and delivery foundation:

- Admins manage the product catalog, license types, licenses, activations, entitlements, and download items.
- Customers view owned products, active licenses, expiry warnings, product details, and allowed downloads.
- The data model is ready for Week 3 public installer API verification and activation endpoints.

## Current Status

- [x] Week 1 foundation remains intact.
- [x] Week 2 feature branch created.
- [x] Week 2 route/controller/view foundation created.
- [x] Day 8 recursive product tree and admin product UI implemented.
- [x] Day 9 product tree UX, breadcrumbs, and safety rules implemented.
- [x] Day 10 license type CRUD and license model helpers implemented.
- [x] Day 11 admin license management implemented.
- [x] Day 12 batch licenses, key reveal, and activation reset implemented.
- [x] Day 13 entitlement management and download item management implemented.
- [x] Day 14 customer portal products, licenses, and downloads implemented.
- [x] Week 2 acceptance checklist passed.
- [x] Week 2 technical deliverable file/folder checklist passed.
- [x] Week 2 handoff workflow is covered by an automated end-to-end feature test.
- [x] Architecture optimization pass is implemented, documented, and regression-checked.
- [ ] Next: manual browser review and Week 2 commit.

## Latest Verification Results

Last acceptance run: 2026-05-08

```text
Deliverable and support path check: 32 paths found, missing none
Focused Week 2 plus handoff tests: 36 passed, 386 assertions, 1.82 seconds
Full PHP test suite: 109 passed, 777 assertions, 5.70 seconds
Vite production build passed, 55 modules transformed, CSS 47.17 kB, JS 89.47 kB
Registered routes: 93
```

Latest edge-case verification:

```text
License edge-case tests: 3 passed, 26 assertions
License model/support regression tests: 12 passed, 108 assertions
Activation-limit helpers and encrypted-key failure behavior verified
```

## Week 2 Technical Deliverables Checklist

Models:

- [x] `app/Models/Product.php`
- [x] `app/Models/LicenseType.php`
- [x] `app/Models/License.php`
- [x] `app/Models/LicenseActivation.php`
- [x] `app/Models/Entitlement.php`
- [x] `app/Models/DownloadItem.php`
- [x] `app/Models/DownloadLog.php`

Admin controllers:

- [x] `app/Http/Controllers/Admin/ProductController.php`
- [x] `app/Http/Controllers/Admin/LicenseTypeController.php`
- [x] `app/Http/Controllers/Admin/LicenseController.php`
- [x] `app/Http/Controllers/Admin/EntitlementController.php`
- [x] `app/Http/Controllers/Admin/DownloadController.php`

Customer controllers:

- [x] `app/Http/Controllers/User/DashboardController.php`
- [x] `app/Http/Controllers/User/LicenseController.php`
- [x] `app/Http/Controllers/User/DownloadController.php`
- [x] `app/Http/Controllers/User/ProductController.php`

Admin views:

- [x] `resources/views/admin/products/`
- [x] `resources/views/admin/license-types/`
- [x] `resources/views/admin/licenses/`
- [x] `resources/views/admin/entitlements/`
- [x] `resources/views/admin/downloads/`

Customer views:

- [x] `resources/views/user/dashboard.blade.php`
- [x] `resources/views/user/licenses/`
- [x] `resources/views/user/downloads/`
- [x] `resources/views/user/products/`

Reusable Blade components:

- [x] `resources/views/components/product-tree.blade.php`
- [x] `resources/views/components/product-breadcrumbs.blade.php`
- [x] `resources/views/components/status-badge.blade.php`
- [x] `resources/views/components/license-key-display.blade.php`
- [x] `resources/views/components/confirm-danger-modal.blade.php`
- [x] `resources/views/components/download-card.blade.php`

Support services:

- [x] `app/Support/ProductTreeBuilder.php`

## Week 2 Handoff Criteria

- [x] Admin can create a recursive product tree.
- [x] Admin can create license types.
- [x] Admin can create a customer license for a product or sub-product.
- [x] Admin can grant the customer an entitlement.
- [x] Admin can create a private download item.
- [x] Customer can log in and view entitled products.
- [x] Customer can view assigned licenses.
- [x] Customer can view available downloads.
- [x] Customer can download a private file through the controller.
- [x] Customer download creates a `download_logs` record.
- [x] Full handoff workflow is covered by `tests/Feature/WeekTwoHandoffWorkflowTest.php`.

## Week 1 Regression Checklist

- [x] Fresh migrations and seed data run.
- [x] Seeded admin account exists.
- [x] Seeded customer account exists.
- [x] Breeze authentication remains functional.
- [x] Slider CAPTCHA remains required before login.
- [x] Admin dashboard remains accessible to admins.
- [x] Customer dashboard remains accessible to customer users.
- [x] Admin is blocked from `/user`.
- [x] Customer is blocked from `/admin`.
- [x] Inactive users are logged out on protected routes.
- [x] Organization CRUD tests pass.
- [x] User CRUD and filters tests pass.

## Week 2 Foundation Checklist

- [x] Admin navigation includes Week 2 modules.
- [x] Customer navigation includes products, licenses, and downloads.
- [x] Admin product route surface exists.
- [x] Admin license type index exists.
- [x] Admin license CRUD exists.
- [x] Admin license reveal endpoint is protected.
- [x] Admin license batch creation exists.
- [x] Admin activation reset and deletion support exists.
- [x] Admin activation index and reset action exist.
- [x] Admin entitlement index exists.
- [x] Admin download item index exists.
- [x] Admin entitlement CRUD exists.
- [x] Admin download item CRUD exists.
- [x] Download uploads are stored under private storage.
- [x] Customer products index and show pages exist.
- [x] Customer licenses index and show pages exist.
- [x] Customer downloads index and private download action exist.
- [x] Customer queries are scoped to the authenticated user.
- [x] License keys are encrypted at rest.
- [x] License key hash lookup helper exists for future API work.
- [x] Default license types are seeded.
- [x] APP_KEY license encryption warning is documented.

## Day 8 Product Tree Checklist

- [x] Product model uses `parent_id`.
- [x] Product nesting supports unlimited depth.
- [x] `parent()` relationship exists.
- [x] `subProducts()` relationship exists.
- [x] `allChildren()` recursive relationship exists.
- [x] `active()` scope exists.
- [x] `main()` scope exists.
- [x] `getAllDescendantIds()` exists.
- [x] `getFlatDescendants(int $depth = 0)` exists.
- [x] Admin `ProductController` supports `index`.
- [x] Admin `ProductController` supports `create`.
- [x] Admin `ProductController` supports `store`.
- [x] Admin `ProductController` supports `show`.
- [x] Admin `ProductController` supports `edit`.
- [x] Admin `ProductController` supports `update`.
- [x] Admin `ProductController` supports `destroy`.
- [x] Product codes are generated from the product name.
- [x] Duplicate product names receive unique generated codes.
- [x] Admin can create top-level products.
- [x] Admin can create child products.
- [x] Admin can view the nested product tree.
- [x] Admin can edit and deactivate products.
- [x] Product parent cycles are blocked.
- [x] Products with children or linked license data are protected from deletion.
- [x] Product tree renders recursive children in the admin UI.

## Day 9 Product Tree UX and Safety Checklist

- [x] Product detail pages show product core fields.
- [x] Product detail pages show parent product context.
- [x] Product detail pages show recursive child products.
- [x] Product detail pages show active/inactive status.
- [x] Product detail pages include related navigation.
- [x] Deeply nested products show breadcrumbs from root to current product.
- [x] Catalog paths render as `Parent / Child / Grandchild`.
- [x] Product index tree shows depth context.
- [x] Product index tree shows catalog path context.
- [x] Parent selector uses indentation and full catalog path context.
- [x] Edit parent selector excludes the product itself.
- [x] Edit parent selector excludes all descendants.
- [x] Backend validation blocks assigning a product to itself.
- [x] Backend validation blocks moving a product into one of its descendants.
- [x] Recursive traversal helpers remain cycle-safe.

## Day 10 License Types and License Model Checklist

- [x] License types are stored in `license_types`, not hardcoded in the license model.
- [x] Default license types are seeded: Trial, Single, Multi, Enterprise, Educational.
- [x] Admin `LicenseTypeController` supports `index`.
- [x] Admin `LicenseTypeController` supports `create`.
- [x] Admin `LicenseTypeController` supports `store`.
- [x] Admin `LicenseTypeController` supports `show`.
- [x] Admin `LicenseTypeController` supports `edit`.
- [x] Admin `LicenseTypeController` supports `update`.
- [x] Admin `LicenseTypeController` supports `destroy`.
- [x] License type codes are normalized to uppercase slug-friendly values.
- [x] License type deletion is blocked when licenses reference the type.
- [x] License keys are encrypted at rest.
- [x] License key hashes are generated automatically for lookup.
- [x] Masked license key display is available.
- [x] Expiry helpers and scopes are available.
- [x] License key generation helper is available.
- [x] APP_KEY warning is documented in `docs/license-key-encryption.md`.

## Day 11 Admin License Management Checklist

- [x] Admin `LicenseController` supports `index`.
- [x] Admin `LicenseController` supports `create`.
- [x] Admin `LicenseController` supports `store`.
- [x] Admin `LicenseController` supports `show`.
- [x] Admin `LicenseController` supports `edit`.
- [x] Admin `LicenseController` supports `update`.
- [x] Admin `LicenseController` supports `destroy`.
- [x] License form can select customer user with name, email, and organization.
- [x] License form can select product from recursive product tree.
- [x] License form can select optional sub-product from the product tree.
- [x] Sub-product validation requires a child or descendant of the selected product.
- [x] License form can select license type.
- [x] License form supports quantity, max activations, expiry date, and license key.
- [x] License form includes a local Generate Key action.
- [x] License index shows masked key, user, organization/client name, products, type, limits, expiry, and actions.
- [x] License detail shows metadata, masked key, product paths, expiry state, and activation placeholder.
- [x] Plaintext keys are not printed into index or detail HTML.
- [x] Plaintext reveal uses a protected admin endpoint.
- [x] Duplicate license keys are blocked by normalized hash validation.
- [x] License deletion works and activation records cascade through database rules.

## Day 12 Batch Licenses, Key Reveal, and Activation Reset Checklist

- [x] `POST /admin/licenses/generate-key` exists.
- [x] `POST /admin/licenses/batch-store` exists.
- [x] `GET /admin/licenses/{license}/show-key` exists.
- [x] `POST /admin/licenses/{license}/reset-activation` exists.
- [x] `DELETE /admin/licenses/activation/{activation}` exists.
- [x] Server-side key generation uses `License::generateKey()`.
- [x] Create form uses the protected server key generator.
- [x] Batch form uses the protected server key generator for a sample key.
- [x] Batch form supports shared license fields and license record count.
- [x] Batch store creates unique encrypted keys for each license record.
- [x] Plaintext key reveal returns a clear APP_KEY history error when decryption fails.
- [x] License detail displays real activation rows.
- [x] Activation rows show device ID, hostname, IP address, location, status, and activated date.
- [x] Admin can delete one activation record.
- [x] Admin can reset all activations by deleting records.
- [x] Admin can reset all activations by marking records inactive.
- [x] Activation reset is visually separated as a support operation.

## Day 13 Entitlements and Download Items Checklist

- [x] Admin `EntitlementController` supports `index`.
- [x] Admin `EntitlementController` supports `create`.
- [x] Admin `EntitlementController` supports `store`.
- [x] Admin `EntitlementController` supports `show`.
- [x] Admin `EntitlementController` supports `edit`.
- [x] Admin `EntitlementController` supports `update`.
- [x] Admin `EntitlementController` supports `destroy`.
- [x] Entitlement statuses are `active`, `expired`, and `suspended`.
- [x] Entitlement UI explains product/download access separately from installer activation.
- [x] One entitlement per user/product pair is enforced in validation and the database.
- [x] Admin `DownloadController` supports `index`.
- [x] Admin `DownloadController` supports `create`.
- [x] Admin `DownloadController` supports `store`.
- [x] Admin `DownloadController` supports `show`.
- [x] Admin `DownloadController` supports `edit`.
- [x] Admin `DownloadController` supports `update`.
- [x] Admin `DownloadController` supports `destroy`.
- [x] Download item forms support private file upload.
- [x] Download item forms support private file registration.
- [x] Uploads are stored under `storage/app/private/downloads`.
- [x] Public paths and path traversal are blocked.
- [x] Download item access rules are used by the customer download flow.
- [x] `DownloadLog::logDownload()` helper exists.

## Day 14 Customer Portal Checklist

- [x] Customer dashboard shows owned products.
- [x] Customer dashboard shows active licenses.
- [x] Customer dashboard shows licenses expiring soon.
- [x] Customer dashboard shows expired licenses.
- [x] Customer dashboard shows available downloads.
- [x] Customer dashboard shows recent download history.
- [x] Customer license index shows only the authenticated user's licenses.
- [x] Customer license detail page exists.
- [x] Customer license detail shows product, license type, masked key, expiry, max activations, and active activation count.
- [x] Customer-side key reveal is implemented through a protected owner-scoped endpoint.
- [x] Customer products show only current entitlements.
- [x] Product hierarchy context is shown through catalog paths.
- [x] Downloads page shows only files allowed by entitlement and file rules.
- [x] Download action checks active entitlement.
- [x] Download action checks entitlement download window.
- [x] Download action checks active download item.
- [x] Download action checks download item expiry.
- [x] Download action checks customer-specific file assignment.
- [x] Download action checks private file existence.
- [x] Download action logs via `DownloadLog::logDownload()`.
- [x] Download action streams from private storage without redirecting to public files.

## Architecture Optimization Checklist

- [x] Repeated admin product-tree option building is centralized in `App\Support\ProductTreeBuilder`.
- [x] Product, license, entitlement, and download admin forms reuse the shared product-tree builder.
- [x] Product detail child tree receives the correct parent path context.
- [x] Product tree child-count badges show the count only.
- [x] Product tree indentation connectors are restored in CSS.
- [x] Customer license activation counts use the shared active activation status constant.
- [x] Optimization regression checks are recorded in `docs/functionality-verification-log.md`.
- [x] Latest full regression report is recorded in `docs/test-reports/2026-05-08-full-regression-test-report.md`.

## Notes and Known Gaps

- Product CRUD, tree safety, license type CRUD, core license model helpers, admin license CRUD, batch issuing, activation support operations, entitlement CRUD, download item CRUD, customer portal download streaming, and product-tree builder reuse are complete.
- Seed data includes demo products, license records, entitlements, and private download metadata for local review.
- Public API endpoints remain placeholders for Week 3.
- Manual browser review is still recommended for deep product trees, reveal modals, batch license UX, activation reset confirmation copy, upload/register download flows, and real private file downloads.
