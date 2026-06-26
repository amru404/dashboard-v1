<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'display_name',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'status',
        'download_expired_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'download_expired_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<User>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
