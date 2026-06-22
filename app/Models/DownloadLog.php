<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'download_item_id',
        'ip_address',
        'downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<DownloadItem, $this>
     */
    public function downloadItem(): BelongsTo
    {
        return $this->belongsTo(DownloadItem::class);
    }

    public static function logDownload(int $userId, int $downloadItemId, ?string $ip): self
    {
        return self::create([
            'user_id' => $userId,
            'download_item_id' => $downloadItemId,
            'ip_address' => $ip,
            'downloaded_at' => now(),
        ]);
    }
}
