<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['cover_image'];

    protected $fillable = [
        'title', 'youtube_url', 'cover_image', 'caption', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Pulls the 11-character video id out of any YouTube link.
     *
     * Static so the admin form can validate a pasted link with the same rule
     * the storefront uses to render it. Accepts every share format YouTube
     * hands out — /shorts/, youtu.be/, /watch?v=, /embed/ — with or without the
     * ?si= and &t= suffixes, and returns null for anything else.
     */
    public static function youtubeIdFrom(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $pattern = '#(?:youtube\.com/(?:shorts/|watch\?(?:.*&)?v=|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})#i';

        return preg_match($pattern, $url, $matches) ? $matches[1] : null;
    }

    public function getYoutubeIdAttribute(): ?string
    {
        return static::youtubeIdFrom($this->youtube_url);
    }

    /**
     * Unlike Instagram's, YouTube's embed plays in place. `autoplay=1` is safe
     * because the iframe is only created after the viewer clicks the poster,
     * so the browser counts it as a user gesture. nocookie keeps YouTube from
     * setting tracking cookies until playback actually starts.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id
            ? "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0&modestbranding=1&playsinline=1"
            : null;
    }

    /**
     * The hover preview: muted, looping, chromeless. Muted is not a style
     * choice — browsers refuse to autoplay anything with sound. `loop` only
     * works when `playlist` repeats the same id, which is a YouTube quirk.
     */
    public function getPreviewUrlAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id
            ? "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&disablekb=1&rel=0&modestbranding=1&playsinline=1"
            : null;
    }

    public function getPermalinkAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id ? "https://www.youtube.com/shorts/{$id}" : $this->youtube_url;
    }

    /**
     * Uploaded cover wins; otherwise YouTube's own thumbnail, so a reel needs
     * nothing but a pasted link. maxres does not exist for every video, which
     * the card handles by falling back to hqdefault on the img error event.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (filled($this->cover_image)) {
            return $this->mediaUrl($this->cover_image);
        }

        $id = $this->youtube_id;

        return $id ? "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg" : null;
    }

    public function getCoverFallbackUrlAttribute(): ?string
    {
        $id = $this->youtube_id;

        return $id ? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg" : null;
    }
}
