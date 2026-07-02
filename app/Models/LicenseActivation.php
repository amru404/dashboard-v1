<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LicenseActivation extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'license_id',
        'device_id',
        'ip_address',
        'hostname',
        'location',
        'status',
        'activated_at',
    ];

    public function setDeviceIdAttribute($value): void
    {
        $this->attributes['device_id'] = self::normalizeDeviceId((string) $value);
    }

    /**
     * @return BelongsTo<License, $this>
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public static function normalizeDeviceId(string $deviceId): string
    {
        return Str::of($deviceId)
            ->replaceMatches('/\s+/', '')
            ->trim()
            ->toString();
    }

    /**
     * @param  Builder<LicenseActivation>  $query
     * @return Builder<LicenseActivation>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * @param  Builder<LicenseActivation>  $query
     * @return Builder<LicenseActivation>
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
}
