<?php

namespace N7olkachev\ComputedProperties\Test\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'viewed_at',
    ];

    public $timestamps = false;
}