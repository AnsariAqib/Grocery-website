<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['image'];

    protected $fillable = [
        'image', 'title_html', 'subtext',
        'btn_primary_text', 'btn_primary_link',
        'btn_navy_text', 'btn_navy_link',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }
}
