# License Key Encryption

Customer Area stores plaintext license keys through Laravel encryption and stores a SHA-256 hash beside them for lookup.

## APP_KEY Warning

Do not run `php artisan key:generate` or otherwise change `APP_KEY` after real license data exists.

Laravel uses `APP_KEY` to encrypt and decrypt `licenses.license_key`. If `APP_KEY` changes, existing encrypted license key values cannot be decrypted by the application anymore.

The `licenses.license_key_hash` column is only for lookup and API verification. It cannot recover or display the original license key.

## Storage Rules

- `licenses.license_key` contains the encrypted license key.
- `licenses.license_key_hash` contains the normalized key's SHA-256 hash.
- Public/API lookup should query by hash instead of decrypting every license row.
- Admin screens may reveal the decrypted key only when support needs it.
