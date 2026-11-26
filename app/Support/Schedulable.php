<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Timed visibility for content that goes live and retires on its own —
 * promotions, gallery images, popups. The model keeps its own scopeActive()
 * (so it stays greppable next to the is_active flag) and narrows it with
 * scopeScheduleWindow(); the accessors here give the admin a plain-English
 * status without every table re-deriving it.
 *
 * All comparisons run in the app timezone, which config/app.php pins to
 * Asia/Kolkata — the same clock the admin types into the date pickers.
 */
trait Schedulable
{
    public const STATUS_LIVE = 'live';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_HIDDEN = 'hidden';

    /**
     * Rows whose publish/expire window contains right now. Blank ends are
     * open: no publish_at means "already live", no expire_at means "forever".
     */
    public function scopeScheduleWindow(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where(fn (Builder $q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', $now))
            ->where(fn (Builder $q) => $q->whereNull('expire_at')->orWhere('expire_at', '>', $now));
    }

    /** Rows the window has already retired — the ones the storefront drops. */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expire_at')->where('expire_at', '<=', now());
    }

    /** Rows waiting for their start time. */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->whereNotNull('publish_at')->where('publish_at', '>', now());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expire_at !== null && $this->expire_at->isPast();
    }

    public function getIsScheduledAttribute(): bool
    {
        return $this->publish_at !== null && $this->publish_at->isFuture();
    }

    /**
     * One of the STATUS_* constants. is_active wins over the schedule: an
     * unticked row is simply off, whatever its dates say.
     */
    public function getScheduleStatusAttribute(): string
    {
        if (! $this->is_active) {
            return self::STATUS_HIDDEN;
        }

        if ($this->is_expired) {
            return self::STATUS_EXPIRED;
        }

        if ($this->is_scheduled) {
            return self::STATUS_SCHEDULED;
        }

        return self::STATUS_LIVE;
    }

    /** ISO-8601 with offset, so the browser can keep counting down locally. */
    public function scheduleIso(?Carbon $at): ?string
    {
        return $at?->toIso8601String();
    }
}
