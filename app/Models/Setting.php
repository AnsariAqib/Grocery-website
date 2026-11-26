<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label', 'help', 'sort_order'];

    private const CACHE_KEY = 'settings.map';

    protected static function booted(): void
    {
        // Any write through the admin invalidates the cached map.
        static::saved(fn () => static::forgetCache());
        static::deleted(fn () => static::forgetCache());
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * key => value for every setting.
     *
     * Deliberately not named get()/value(): those are Eloquent builder methods
     * reachable statically, and shadowing them would break normal queries.
     */
    public static function map(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::query()->pluck('value', 'key')->all()
        );
    }

    public static function val(string $key, mixed $default = null): mixed
    {
        return static::map()[$key] ?? $default;
    }

    /**
     * "+91 9879197739 / 9090081417", one number on its own, or an empty
     * string when neither is filled in — never a bare "+91".
     */
    public static function phoneDisplay(): string
    {
        $numbers = array_filter([
            trim((string) static::val('phone_primary', '')),
            trim((string) static::val('phone_secondary', '')),
        ], 'filled');

        return $numbers === [] ? '' : '+91 '.implode(' / ', $numbers);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::val($key);

        return $value === null
            ? $default
            : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
