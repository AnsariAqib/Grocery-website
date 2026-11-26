<?php

namespace App\Models;

use App\Support\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UspCard extends Model
{
    use Publishable;

    public const SCOPE_STORE = 'store';

    public const SCOPE_B2B = 'b2b';

    public const SCOPE_BOTH = 'both';

    protected $fillable = ['scope', 'icon', 'title', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Cards marked "both" show on either category page.
     *
     * Deliberately *not* called `forPage`: a local scope wins over the query
     * builder's own method of the same name, and `paginate()` calls
     * `forPage($page, $perPage)` internally. Named that way, every paginated
     * read of this table quietly became `whereIn('scope', [1, 'both'])` — which
     * is why the admin list only ever showed the "Both pages" cards while the
     * counter underneath said there were six.
     */
    public function scopeShownOn(Builder $query, string $scope): Builder
    {
        return $query->whereIn('scope', [$scope, self::SCOPE_BOTH]);
    }
}
