<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['image'];

    public const TYPE_CLIENT = 'client';

    public const TYPE_BRAND = 'brand';

    protected $fillable = ['type', 'name', 'image', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }
}
