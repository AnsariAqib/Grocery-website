<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['image'];

    public const TYPE_STORE = 'store';

    public const TYPE_B2B = 'b2b';

    protected $fillable = [
        'name', 'slug', 'type', 'icon', 'icon_color',
        'home_label', 'image', 'badge_bg', 'sort_order', 'is_active', 'is_featured',
    ];

    protected $casts = ['is_active' => 'boolean', 'is_featured' => 'boolean'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Subcategory::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }
}
