<?php

namespace App\Models;

use App\Support\Publishable;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use Publishable;

    protected $fillable = [
        'name', 'role', 'quote', 'rating', 'tag', 'icon', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * The flat shape Components/Testimonials.vue consumes.
     */
    public function toCardArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'quote' => $this->quote,
            // The card draws this many stars, so it has to stay inside the row.
            'rating' => max(1, min(5, (int) ($this->rating ?: 5))),
            'tag' => $this->tag,
            'icon' => $this->icon ?: 'fa-user-tie',
        ];
    }
}
