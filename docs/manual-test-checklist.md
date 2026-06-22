# Manual Test Checklist

Use this as the permanent browser testing reference for Customer Area.

The checklist uses Markdown task boxes (`- [ ]`). WebStorm, GitHub, and many Markdown editors can toggle them directly.

## Reset All Checkboxes

From the project root:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\reset-manual-checklist.ps1
```

If you do not want to run the helper script, use this one-liner from the project root:

```powershell
(Get-Content docs\manual-test-checklist.md) -replace '^(\s*-\s*)\[[xX]\]', '$1[ ]' | Set-Content docs\manual-test-checklist.md
```

## Local Test Setup

- [ ] Open a terminal in `customer-area`.
- [ ] Run `C:\xampp\php\php.exe artisan migrate:fresh --seed` if a clean database is acceptable.
- [ ] Run `cmd /c npm run dev`.
- [ ] Run `C:\xampp\php\php.exe artisan serve`.
- [ ] Open `http://127.0.0.1:8000`.
- [ ] Confirm `/` redirects to `/login`.

Seeded accounts:

- Admin: `admin@example.com` / `password`
- Customer: `user@example.com` / `password`

## Authentication And Roles

- [ ] Login page uses the branded green/white Madani-style layout.
- [ ] Slider CAPTCHA is visible on login.
- [ ] Login without solving CAPTCHA is blocked.
- [ ] Incorrect slider attempt shows a clear error.
- [ ] Correct slider attempt verifies successfully.
- [ ] Admin login redirects to `/admin`.
- [ ] Customer login redirects to `/user`.
- [ ] CAPTCHA resets after successful login.
- [ ] Guest access to `/admin` redirects to login.
- [ ] Guest access to `/user` redirects to login.
- [ ] Admin is blocked from `/user` with 403.
- [ ] Customer is blocked from `/admin` with 403.
- [ ] Inactive user is logged out on the next protected request.
- [ ] Logout works from admin layout.
- [ ] Logout works from customer layout.

## Admin Navigation

- [ ] Header shows Dashboard, Catalog, Access, and Users groups.
- [ ] Catalog dropdown opens on hover.
- [ ] Access dropdown opens on hover.
- [ ] Users dropdown opens on hover.
- [ ] Dropdowns stay open long enough for the cursor to reach links.
- [ ] Active dropdown/group styling is clear.
- [ ] Mobile navigation is usable at narrow width.
- [ ] Dashboard link returns to `/admin`.

## Admin Dashboard

- [ ] Admin dashboard loads without errors.
- [ ] Organization count card renders.
- [ ] User count card renders.
- [ ] Product count card renders.
- [ ] License count card renders.
- [ ] Dashboard cards use consistent white card styling.

## Organization Management

- [ ] Organization index loads.
- [ ] Create organization button is visible.
- [ ] Create organization with valid name/code succeeds.
- [ ] Duplicate organization code is blocked.
- [ ] Invalid organization email is blocked.
- [ ] Organization detail page shows name, code, contact fields, and status.
- [ ] Organization detail page lists related users when available.
- [ ] Edit organization saves changes.
- [ ] Deactivating organization updates the inactive badge.
- [ ] Deleting organization without users succeeds.
- [ ] Deleting organization with users is blocked with a clear message.

## User Management

- [ ] User index loads.
- [ ] Search by name works.
- [ ] Search by email works.
- [ ] Organization filter works.
- [ ] Role filter works.
- [ ] Active/inactive filter works.
- [ ] Create user form includes organization, name, email, password, role, and status.
- [ ] Creating a customer user without organization is blocked.
- [ ] Creating a user with duplicate email is blocked.
- [ ] Creating a user with valid password succeeds.
- [ ] User detail page shows organization, role, email, and status.
- [ ] Editing user without a new password keeps the current password.
- [ ] Editing user with a new password updates the password.
- [ ] Current admin cannot delete their own account.
- [ ] Current admin cannot deactivate their own account.

## Product Management

- [ ] Product index loads.
- [ ] Product index shows search bar.
- [ ] Product index shows status filter.
- [ ] Product index shows level filter.
- [ ] Product index shows Expand all and Collapse all controls.
- [ ] Product index does not show depth badges.
- [ ] Product index does not show full product paths.
- [ ] Product tree indentation lines are visible.
- [ ] Product tree indentation connector lines are continuous at nested levels.
- [ ] Product tree child-count badges show only the count number.
- [ ] Create top-level product succeeds.
- [ ] Create child product succeeds.
- [ ] Create grandchild product succeeds.
- [ ] Products can be nested more than two levels deep.
- [ ] Expand all reveals nested products.
- [ ] Collapse all hides nested products.
- [ ] Individual branch collapse/expand works.
- [ ] Search by product name works.
- [ ] Search by product code works.
- [ ] Search expands matching nested branches.
- [ ] Active-only filter works.
- [ ] Inactive-only filter works.
- [ ] Top-level-only filter works.
- [ ] Child-products filter works.
- [ ] Inactive products are visually distinct.
- [ ] Product detail page shows breadcrumbs.
- [ ] Child product detail page shows full product path.
- [ ] Product detail child tree keeps correct parent path context.
- [ ] Top-level product detail page does not need a redundant path value.
- [ ] Product detail page shows parent info for child products.
- [ ] Product detail page lists direct children.
- [ ] Edit product excludes itself from the parent selector.
- [ ] Edit product excludes descendants from the parent selector.
- [ ] Backend blocks assigning product as its own parent.
- [ ] Backend blocks moving product into one of its descendants.
- [ ] Product delete button uses outline red styling and trash icon.
- [ ] Deleting a parent product with children is blocked.
- [ ] Deleting a product linked to licenses, entitlements, or downloads is blocked.
- [ ] Deleting a leaf product with no linked records succeeds.

## License Type Management

- [ ] License type index loads.
- [ ] Default types exist: Trial, Single, Multi, Enterprise, Educational.
- [ ] Create license type succeeds.
- [ ] Duplicate license type code is blocked.
- [ ] Edit license type succeeds.
- [ ] Deactivate license type updates status.
- [ ] Delete unused license type succeeds.
- [ ] Delete license type linked to licenses is blocked.
- [ ] License forms read types from the database, not hardcoded enum values.

## Admin License Management

- [ ] License index loads.
- [ ] Create license form loads.
- [ ] User selector displays name, email, and organization.
- [ ] Product selector shows recursive product indentation.
- [ ] Product selector hierarchy/path labels remain correct after optimization.
- [ ] License type selector uses database license types.
- [ ] Generate key action fills or returns a license key.
- [ ] Create license with generated key succeeds.
- [ ] Create license with manually entered key succeeds.
- [ ] Duplicate license key is blocked.
- [ ] License key is shown masked in index.
- [ ] License detail page shows customer, organization, product, type, quantity, max activations, and expiry.
- [ ] License detail page shows masked key by default.
- [ ] Reveal key action returns plaintext only after admin action.
- [ ] Reveal key action handles decrypt failure with a clear APP_KEY history message.
- [ ] Editing license without a new key keeps the existing key.
- [ ] Editing license with a new key rotates encrypted key and hash.
- [ ] Expired license displays expired status.
- [ ] Expiring-soon license displays warning state.
- [ ] Deleting license removes related activations through cascade rules.

## Activation Support

- [ ] License detail page shows activation support section.
- [ ] License detail page shows activation rows when records exist.
- [ ] Activation rows show device ID, hostname, IP address, location, status, and activation date.
- [ ] Reset all activations is clearly labeled as a support action.
- [ ] Reset strategy `delete` removes activation records.
- [ ] Reset strategy `deactivate` marks activation records inactive.
- [ ] Deleting one activation removes only that activation.
- [ ] Activation count reflects active activation records only.
- [ ] License activation limit prevents excess active devices.
- [ ] Same device ID does not incorrectly consume multiple activation slots.
- [ ] Unlimited activation license allows activation beyond normal limits.
- [ ] Expired license cannot be treated as active.

## Entitlement Management

- [ ] Entitlement index loads.
- [ ] Create entitlement form loads.
- [ ] Product selector shows recursive hierarchy and correct path labels.
- [ ] Create entitlement for user/product succeeds.
- [ ] Duplicate user/product entitlement is blocked.
- [ ] Start date is required and saved correctly.
- [ ] End date is optional and saved correctly.
- [ ] Download expiry date is optional and saved correctly.
- [ ] Status supports active, expired, and suspended.
- [ ] Entitlement detail explains that entitlement controls access/downloads, not installer activation.
- [ ] Edit entitlement succeeds.
- [ ] Active entitlement allows download access when windows are valid.
- [ ] Expired entitlement blocks download access.
- [ ] Suspended entitlement blocks download access.
- [ ] Expired download window blocks download access.
- [ ] Delete entitlement succeeds.

## Admin Download Management

- [ ] Download item index loads.
- [ ] Create download item form loads.
- [ ] Download item can attach to a product.
- [ ] Download product selector shows recursive hierarchy and correct path labels.
- [ ] Download item can optionally be restricted to one user.
- [ ] Uploading a file stores it under `storage/app/private/downloads`.
- [ ] Registering an existing private path works when file exists.
- [ ] Public paths are rejected or not offered.
- [ ] File name is stored.
- [ ] File path is stored as a private relative path.
- [ ] File size is stored.
- [ ] Version is stored.
- [ ] Expiry date is stored.
- [ ] Active/inactive status is stored.
- [ ] Download detail page shows private path.
- [ ] Download detail page shows recent download logs when present.
- [ ] Edit download item succeeds.
- [ ] Inactive download item is visually distinct.
- [ ] Expired download item is visually distinct.
- [ ] Delete download item succeeds when safe.

## Customer Dashboard

- [ ] Customer dashboard loads at `/user`.
- [ ] Dashboard shows owned products.
- [ ] Dashboard shows active licenses.
- [ ] Dashboard shows licenses expiring soon.
- [ ] Dashboard shows expired licenses.
- [ ] Dashboard shows available downloads.
- [ ] Dashboard shows recent download history when available.
- [ ] Dashboard does not show another user's products.
- [ ] Dashboard does not show another user's licenses.
- [ ] Dashboard does not show another user's downloads.

## Customer Products

- [ ] Product list loads at `/user/products`.
- [ ] Customer sees only entitled products.
- [ ] Customer can open an entitled product detail page.
- [ ] Product detail shows hierarchy/breadcrumb context.
- [ ] Customer entitled to a child product can understand its parent path.
- [ ] Customer cannot open an unentitled product detail page.
- [ ] Inactive or inaccessible product state is handled clearly.

## Customer Licenses

- [ ] License list loads at `/user/licenses`.
- [ ] Customer sees only their own licenses.
- [ ] License detail page loads for owned license.
- [ ] License detail shows product, license type, masked key, expiry, max activations, and active activation count.
- [ ] Customer cannot open another user's license.
- [ ] Customer-side reveal key behavior is either clearly allowed or clearly unavailable.
- [ ] Expired and expiring licenses display clear status.

## Customer Downloads

- [ ] Download list loads at `/user/downloads`.
- [ ] Customer sees only downloads allowed by active entitlement.
- [ ] Customer does not see files for products without entitlement.
- [ ] Customer does not see another user's user-specific download item.
- [ ] Customer does not see inactive download items.
- [ ] Customer does not see expired download items.
- [ ] Customer cannot download when entitlement status is expired.
- [ ] Customer cannot download when entitlement status is suspended.
- [ ] Customer cannot download after entitlement download window expires.
- [ ] Download action checks private file exists.
- [ ] Download action streams file from private storage.
- [ ] Download action does not redirect to a public file URL.
- [ ] Successful download creates a `download_logs` row.
- [ ] Failed unauthorized download does not create a misleading success log.

## Security And Data Integrity

- [ ] Downloadable installer files are not stored under `public/`.
- [ ] Private downloads are served only through controller authorization.
- [ ] License plaintext keys are not printed in table rows.
- [ ] License key column is encrypted in the database.
- [ ] License key hash column is populated.
- [ ] APP_KEY warning is documented in `docs/license-key-encryption.md`.
- [ ] Do not regenerate `APP_KEY` after real license data exists.
- [ ] Admin routes require authenticated admin role.
- [ ] Customer routes require authenticated user role.
- [ ] Public API remains placeholder-only until Week 3.

## Responsive And UI Review

- [ ] Login page looks polished at desktop width.
- [ ] Login page looks polished at mobile width.
- [ ] Admin dashboard cards do not overlap on mobile.
- [ ] Admin tables remain usable with horizontal scrolling where needed.
- [ ] Forms have consistent labels, input radius, spacing, and focus states.
- [ ] Buttons use consistent green/white/outline styling.
- [ ] Delete buttons are not filled red unless the action is intentionally critical.
- [ ] Status badges are clear and consistent.
- [ ] No obvious default Breeze styling remains on key pages.
- [ ] No text overlaps or clips inside cards, buttons, forms, or navigation.

## Automated Regression Commands

- [ ] Run `C:\xampp\php\php.exe artisan migrate:fresh --seed`.
- [ ] Run focused Week 2 feature tests plus `tests\Feature\WeekTwoHandoffWorkflowTest.php`.
- [ ] Run `C:\xampp\php\php.exe artisan test`.
- [ ] Run `npm.cmd run build`.
- [ ] Run `C:\xampp\php\php.exe artisan route:list` if route changes were made.
- [ ] Confirm deliverable/support path check finds 32 paths with no missing files.
- [ ] Confirm there are no unexpected errors in `storage/logs/laravel.log`.

## Debugging Helpers

- [ ] If routes behave unexpectedly, run `C:\xampp\php\php.exe artisan route:list`.
- [ ] If views look stale, run `C:\xampp\php\php.exe artisan optimize:clear`.
- [ ] If Vite assets fail, restart `cmd /c npm run dev`.
- [ ] If login/session behavior is odd, clear browser cookies for `127.0.0.1`.
- [ ] If database state is confusing, run `C:\xampp\php\php.exe artisan migrate:fresh --seed`.
- [ ] If downloads fail, confirm the file exists under `storage/app/private/downloads`.
- [ ] If key reveal fails, confirm `APP_KEY` has not changed since the license was created.

## Handoff Sign-Off

- [ ] Manual browser review completed.
- [ ] Automated tests passed.
- [ ] Vite build passed.
- [ ] Known issues are documented.
- [ ] Screens or notes for any failed checklist items are captured.
- [ ] Ready for commit or handoff.
