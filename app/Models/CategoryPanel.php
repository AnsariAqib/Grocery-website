<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;

class CategoryPanel extends Model
{
    use CompressesUploadedImages, HasMediaUrl;

    protected array $compressibleImages = ['art_image'];

    protected $fillable = [
        'type', 'badge_text', 'heading', 'subheading',
        'art_image', 'cta_text', 'cta_url', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getArtImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->art_image);
    }
}
