<?php

namespace App\Models;

use App\Support\Publishable;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use Publishable;

    protected $fillable = ['label', 'target', 'suffix', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'target' => 'integer',
        'is_active' => 'boolean',
    ];
}
