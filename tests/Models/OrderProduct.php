<?php

namespace N7olkachev\ComputedProperties\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use N7olkachev\ComputedProperties\ComputedProperties;

class OrderProduct extends Model
{
    protected $fillable = [
        'price',
        'count',
    ];
}