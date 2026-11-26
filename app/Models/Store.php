<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['image'];

    protected $fillable = [
        'name', 'type', 'image', 'gallery', 'address', 'phone',
        'phone_links', 'gst_number', 'email',
        'map_query', 'latitude', 'longitude', 'map_url',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'phone_links' => 'array',
        'gallery' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        // A link copied out of Google Maps usually carries the coordinates in
        // it, so pasting one is enough to pin the store — nobody has to type
        // the numbers. Short links (maps.app.goo.gl) carry none, and those
        // still work because the pasted link itself is what we open.
        static::saving(function (Store $store) {
            if (blank($store->map_url) || $store->hasCoordinates()) {
                return;
            }

            if ($coords = self::coordinatesFromUrl($store->map_url)) {
                [$store->latitude, $store->longitude] = $coords;
            }
        });
    }

    /**
     * Pull "lat,lng" out of a Google Maps URL.
     *
     * Covers the shapes the app produces: a !3d…!4d… place marker, the
     * /@lat,lng,17z camera position, and a ?q= / ?query= parameter.
     */
    public static function coordinatesFromUrl(string $url): ?array
    {
        $patterns = [
            '/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/',
            '/@(-?\d+\.\d+),(-?\d+\.\d+)/',
            '/[?&](?:q|query|destination|ll)=(-?\d+\.\d+)(?:,|%2C)\s*(-?\d+\.\d+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match($pattern, $url, $m)) {
                continue;
            }

            $lat = (float) $m[1];
            $lng = (float) $m[2];

            if (abs($lat) <= 90 && abs($lng) <= 180) {
                return [$lat, $lng];
            }
        }

        return null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }

    /**
     * Every photo of the outlet, main image first, ready for the card's
     * rotating gallery. Duplicates are dropped so re-uploading the cover shot
     * into the gallery does not make it appear twice.
     */
    public function getGalleryUrlsAttribute(): array
    {
        return collect([$this->image])
            ->merge($this->gallery ?? [])
            ->filter()
            ->map(fn (string $path) => $this->mediaUrl($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function hasCoordinates(): bool
    {
        return filled($this->latitude) && filled($this->longitude);
    }

    /**
     * Search terms used when no pin is set. The outlet name is always part of
     * them, because a search that names the business lands on its listing
     * while a bare street address lands somewhere along the street.
     */
    public function mapSearchTerm(): string
    {
        $query = trim((string) ($this->map_query ?: $this->address));
        $name = trim((string) $this->name);

        if ($name !== '' && ! str_contains(mb_strtolower($query), mb_strtolower($name))) {
            $query = $name.', '.$query;
        }

        return $query;
    }

    /**
     * Link that opens the store on the map.
     */
    public function getMapLinkAttribute(): string
    {
        if (filled($this->map_url)) {
            return $this->map_url;
        }

        if ($this->hasCoordinates()) {
            return 'https://www.google.com/maps/search/?api=1&query='
                .$this->latitude.'%2C'.$this->longitude;
        }

        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($this->mapSearchTerm());
    }

    /**
     * Turn-by-turn directions from wherever the visitor is.
     */
    public function getDirectionsLinkAttribute(): string
    {
        if ($this->hasCoordinates()) {
            return 'https://www.google.com/maps/dir/?api=1&destination='
                .$this->latitude.'%2C'.$this->longitude;
        }

        return 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($this->mapSearchTerm());
    }

    /**
     * Keyless embedded map used for the in-page preview.
     */
    public function getMapEmbedLinkAttribute(): string
    {
        $query = $this->hasCoordinates()
            ? $this->latitude.','.$this->longitude
            : $this->mapSearchTerm();

        return 'https://maps.google.com/maps?q='.rawurlencode($query).'&z=17&output=embed';
    }
}
