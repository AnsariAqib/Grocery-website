<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    /** Only ever written once, at the moment of the visit. */
    public const UPDATED_AT = null;

    protected $fillable = [
        'page', 'path', 'category_id', 'subcategory_id', 'product_id',
        'visitor_hash', 'is_mobile', 'referrer_host', 'created_at',
    ];

    protected $casts = [
        'is_mobile' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
