<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sub_product_id',
        'user_id',
        'license_type_id',
        'license_key',
        'client_name',
        'quantity',
        'max_activations',
        'expired_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'max_activations' => 'integer',
            'expired_date' => 'date',
        ];
    }

    public function setLicenseKeyAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['license_key'] = null;
            $this->attributes['license_key_hash'] = null;

            return;
        }

        $normalizedKey = self::normalizeLicenseKey((string) $value);

        $this->attributes['license_key_hash'] = hash('sha256', $normalizedKey);
        $this->attributes['license_key'] = Crypt::encryptString($normalizedKey);
    }

    public function getLicenseKeyAttribute($value): ?string
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getMaskedLicenseKeyAttribute(): string
    {
        $key = $this->license_key;

        if (! $key) {
            return 'Unavailable';
        }

        return '****-****-****-'.substr($key, -4);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function subProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sub_product_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<LicenseType, $this>
     */
    public function licenseType(): BelongsTo
    {
        return $this->belongsTo(LicenseType::class);
    }

    /**
     * @return HasMany<LicenseActivation, $this>
     */
    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    /**
     * @return HasMany<LicenseActivation, $this>
     */
    public function activeActivations(): HasMany
    {
        return $this->activations()->active();
    }

    public static function normalizeLicenseKey(string $licenseKey): string
    {
        return Str::upper(Str::of($licenseKey)->replaceMatches('/\s+/', '')->toString());
    }

    public static function licenseKeyHash(string $licenseKey): string
    {
        return hash('sha256', self::normalizeLicenseKey($licenseKey));
    }

    public static function generateKey(): string
    {
        return collect(range(1, 4))
            ->map(fn () => strtoupper(bin2hex(random_bytes(2))))
            ->implode('-');
    }

    public function revealLicenseKey(): ?string
    {
        return $this->license_key;
    }

    public function activeActivationCount(): int
    {
        if ($this->relationLoaded('activations')) {
            return $this->activations
                ->where('status', LicenseActivation::STATUS_ACTIVE)
                ->count();
        }

        return $this->activeActivations()->count();
    }

    public function remainingActivations(): ?int
    {
        if ($this->max_activations === null) {
            return null;
        }

        return max(0, $this->max_activations - $this->activeActivationCount());
    }

    public function activationLimitReached(): bool
    {
        return $this->max_activations !== null
            && $this->activeActivationCount() >= $this->max_activations;
    }

    public function hasActiveActivationForDevice(string $deviceId): bool
    {
        $normalizedDeviceId = LicenseActivation::normalizeDeviceId($deviceId);

        if ($normalizedDeviceId === '') {
            return false;
        }

        if ($this->relationLoaded('activations')) {
            return $this->activations
                ->contains(fn (LicenseActivation $activation): bool => $activation->status === LicenseActivation::STATUS_ACTIVE
                    && $activation->device_id === $normalizedDeviceId);
        }

        return $this->activeActivations()
            ->where('device_id', $normalizedDeviceId)
            ->exists();
    }

    public function canActivateDevice(?string $deviceId): bool
    {
        if (blank($deviceId) || $this->isExpired()) {
            return false;
        }

        if ($this->hasActiveActivationForDevice($deviceId)) {
            return true;
        }

        return ! $this->activationLimitReached();
    }

    public function isExpired(): bool
    {
        return $this->expired_date !== null
            && now()->toDateString() > $this->expired_date->toDateString();
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expired_date
            ? (int) now()->startOfDay()->diffInDays($this->expired_date->copy()->startOfDay(), false)
            : null;
    }

    /**
     * @param  Builder<License>  $query
     * @return Builder<License>
     */
    public function scopeWhereLicenseKey(Builder $query, string $licenseKey): Builder
    {
        return $query->where('license_key_hash', self::licenseKeyHash($licenseKey));
    }

    /**
     * @param  Builder<License>  $query
     * @return Builder<License>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('expired_date')
                ->orWhereDate('expired_date', '>=', now()->toDateString());
        });
    }

    /**
     * @param  Builder<License>  $query
     * @return Builder<License>
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query
            ->whereNotNull('expired_date')
            ->whereDate('expired_date', '<', now()->toDateString());
    }

    /**
     * @param  Builder<License>  $query
     * @return Builder<License>
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query
            ->whereNotNull('expired_date')
            ->whereBetween('expired_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString(),
            ]);
    }
}
