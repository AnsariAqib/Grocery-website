<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['certificate_image', 'logo_image'];

    const TYPE_ORIGINAL = 'original';

    const TYPE_CUSTOM = 'custom';

    protected $fillable = [
        'type', 'brand_name', 'title', 'description', 'signatory',
        'logo_image', 'certificate_image', 'pdf_file', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getLogoImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->logo_image);
    }

    public function getCertificateImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->certificate_image);
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->pdf_file);
    }
}
