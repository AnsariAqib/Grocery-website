<?php

namespace App\Models;

use App\Support\Publishable;
use Illuminate\Database\Eloquent\Model;

class OrderStep extends Model
{
    use Publishable;

    protected $fillable = ['icon', 'title', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
