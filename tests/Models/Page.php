<?php

namespace N7olkachev\ComputedProperties\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use N7olkachev\ComputedProperties\ComputedProperties;

class Page extends Model
{
    use ComputedProperties;

    protected $casts = [
        'last_view' => 'datetime',
        'first_view' => 'datetime',
    ];

    public function views()
    {
        return $this->hasMany(PageView::class);
    }

    public function computedLastView($page)
    {
        return PageView::select(new Expression('max(viewed_at)'))
            ->where('page_id', $page->id);
    }

    public function computedFirstView($page)
    {
        // We will return QueryBuilder instead of EloquentBuilder just for testing

        return PageView::select(new Expression('min(viewed_at)'))
            ->where('page_id', $page->id)
            ->toBase();
    }
}