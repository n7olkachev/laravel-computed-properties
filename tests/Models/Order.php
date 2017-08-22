<?php

namespace N7olkachev\ComputedProperties\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use N7olkachev\ComputedProperties\ComputedProperties;

class Order extends Model
{
    use ComputedProperties;

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function computedSum($order)
    {
        return OrderProduct::select(new Expression('sum(price * count)'))
            ->where('order_id', $order->id);
    }
}