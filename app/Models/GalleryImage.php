<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Support\Schedulable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * One item in the gallery: an uploaded photo, or a YouTube video named by its
 * link. Nothing is ever uploaded for a video — the link is the whole record,
 * and YouTube supplies the still that stands in for it until the visitor plays
 * it.
 */
class GalleryImage extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable, Schedulable;

    public const TYPE_PHOTO = 'photo';

    public const TYPE_VIDEO = 'video';

    protected array $compressibleImages = ['image'];

    protected $fillable = [
        'media_type', 'image', 'youtube_url', 'title', 'caption',
        'sort_order', 'is_active', 'publish_at', 'expire_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->scheduleWindow();
    }

    public function scopePhotos(Builder $query): Builder
    {
        return $query->where('media_type', '!=', self::TYPE_VIDEO);
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('media_type', self::TYPE_VIDEO);
    }

    public function isVideo(): bool
    {
        return $this->media_type === self::TYPE_VIDEO;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }

    public function getYoutubeIdAttribute(): ?string
    {
        return Reel::youtubeIdFrom($this->youtube_url);
    }

    /**
     * Played in place when the visitor clicks the still. Autoplay is allowed
     * because the iframe only exists after that click, so the browser counts it
     * as a user gesture; nocookie keeps YouTube from setting anything until
     * playback starts.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id
            ? "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0&modestbranding=1&playsinline=1"
            : null;
    }

    /**
     * The still: an uploaded one if the admin wanted a particular frame,
     * otherwise YouTube's own, so a video needs nothing but its link.
     */
    public function getPosterUrlAttribute(): ?string
    {
        if (filled($this->image)) {
            return $this->image_url;
        }

        $id = $this->youtube_id;

        return $id ? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg" : null;
    }

    /**
     * The flat shape the gallery components consume.
     */
    public function toCardArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->isVideo() ? self::TYPE_VIDEO : self::TYPE_PHOTO,
            // `image` stays the poster for both kinds, so a photo grid, a
            // lightbox and the home rail can all read one field.
            'image' => $this->isVideo() ? $this->poster_url : $this->image_url,
            'embedUrl' => $this->isVideo() ? $this->embed_url : null,
            'title' => $this->title,
            'caption' => $this->caption,
        ];
    }
}
