<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DownloadItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'version',
        'expired_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'expired_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<DownloadLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_date !== null
            && now()->toDateString() > $this->expired_date->toDateString();
    }

    public function isAvailableForUser(User $user): bool
    {
        return $this->is_active
            && ! $this->isExpired()
            && ($this->user_id === null || (int) $this->user_id === (int) $user->id)
            && $user->entitlements()
                ->downloadCurrent()
                ->where('product_id', $this->product_id)
                ->exists();
    }

    /**
     * @param  Builder<DownloadItem>  $query
     * @return Builder<DownloadItem>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<DownloadItem>  $query
     * @return Builder<DownloadItem>
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('expired_date')
                ->orWhereDate('expired_date', '>=', now()->toDateString());
        });
    }

    /**
     * @param  Builder<DownloadItem>  $query
     * @return Builder<DownloadItem>
     */
    public function scopeAvailableForUser(Builder $query, User $user): Builder
    {
        return $query
            ->active()
            ->notExpired()
            ->where(function (Builder $query) use ($user): void {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->whereExists(function ($query) use ($user): void {
                $query->selectRaw('1')
                    ->from('entitlements')
                    ->whereColumn('entitlements.product_id', 'download_items.product_id')
                    ->where('entitlements.user_id', $user->id)
                    ->where('entitlements.status', Entitlement::STATUS_ACTIVE)
                    ->whereDate('entitlements.start_date', '<=', now()->toDateString())
                    ->where(function ($query): void {
                        $query->whereNull('entitlements.end_date')
                            ->orWhereDate('entitlements.end_date', '>=', now()->toDateString());
                    })
                    ->where(function ($query): void {
                        $query->whereNull('entitlements.download_expired_date')
                            ->orWhereDate('entitlements.download_expired_date', '>=', now()->toDateString());
                    });
            });
    }
}
