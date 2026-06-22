<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entitlement extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'download_expired_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'download_expired_date' => 'date',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_EXPIRED,
            self::STATUS_SUSPENDED,
        ];
    }

    public function isCurrent(): bool
    {
        $today = now()->toDateString();

        return $this->status === self::STATUS_ACTIVE
            && $this->start_date->toDateString() <= $today
            && ($this->end_date === null || $this->end_date->toDateString() >= $today);
    }

    public function allowsDownloads(): bool
    {
        return $this->isCurrent()
            && ($this->download_expired_date === null || $this->download_expired_date->toDateString() >= now()->toDateString());
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param  Builder<Entitlement>  $query
     * @return Builder<Entitlement>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * @param  Builder<Entitlement>  $query
     * @return Builder<Entitlement>
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query
            ->active()
            ->whereDate('start_date', '<=', now()->toDateString())
            ->where(function (Builder $query): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            });
    }

    /**
     * @param  Builder<Entitlement>  $query
     * @return Builder<Entitlement>
     */
    public function scopeDownloadCurrent(Builder $query): Builder
    {
        return $query
            ->current()
            ->where(function (Builder $query): void {
                $query->whereNull('download_expired_date')
                    ->orWhereDate('download_expired_date', '>=', now()->toDateString());
            });
    }
}
