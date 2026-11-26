<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared scopes for the content tables, all of which carry is_active
 * and sort_order and are always read in the same way.
 */
trait Publishable
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Rows the admin has flagged for a "featured" showcase. Only meaningful on
     * the tables that carry the column (categories, products).
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
