# Computed properties for Eloquent

<p align="center">
<a href="https://websecret.by"><img src="https://websecret.by/images/logo-github.png" /></a>
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/n7olkachev/laravel-computed-properties.svg?style=flat-square)](https://packagist.org/packages/n7olkachev/laravel-computed-properties)
[![Licence](https://img.shields.io/packagist/l/n7olkachev/laravel-computed-properties.svg?style=flat-square)](https://packagist.org/packages/n7olkachev/laravel-computed-properties)
[![Build Status](https://travis-ci.org/n7olkachev/laravel-computed-properties.svg?branch=master)](https://travis-ci.org/n7olkachev/laravel-computed-properties)

**Laravel 5.4+**

Based on this tweet: https://twitter.com/reinink/status/899713609722449920

Some examples for better understanding of this power:

```php
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
```

Now, we can get order sum with `$order->sum`. Yep, we can get this functionality with `getSumAttribute` but wait! The real power of this package is that we can use this method inside our queries:

```php
$orders = Order::withComputed('sum')->get()
```

We eager loaded `sum` attribute without `N+1` problem.

But there is more! You can add `having` or `orderBy` clauses to such queries for filtering and sorting!

```php
Order::withComputed('sum')->orderBy('sum', 'desc')->get()
```

## Installation

You can install the package via composer:

``` bash
composer require n7olkachev/laravel-computed-properties
```

Next, add ComputedProperties trait to your models:

```php
use ComputedProperties;
```

That's all!

## More examples

```php
class Page extends Model
{
    use ComputedProperties;

    public $timestamps = false;

    protected $casts = [
        'last_view' => 'datetime',
        'first_view' => 'datetime',
    ];

    public function computedLastView($page)
    {
        return PageView::select(new Expression('max(viewed_at)'))
            ->where('page_id', $page->id);
    }

    public function computedFirstView($page)
    {
        return PageView::select(new Expression('min(viewed_at)'))
            ->where('page_id', $page->id);
    }
}
```

We can find `Page` by its first view:

```php
$page = Page::withComputed('first_view')
    ->having('first_view', Carbon::create(2017, 8, 16, 0, 0, 0))
    ->first();
```

Or by both `first_view` and `last_view`

```php
$page = Page::withComputed(['first_view', 'last_view'])
    ->having('first_view', Carbon::create(2017, 8, 16, 0, 0, 0))
    ->having('last_view', Carbon::create(2017, 8, 21, 0, 0, 0))
    ->first();
```

We can order pages by theirs `last_view`
```php
$pages = Page::withComputed('last_view')
    ->orderBy('last_view', 'desc')
    ->get()
```


## Testing

``` bash
$ composer test
```

## Credits

- [Jonathan Reinink](https://github.com/reinink) (idea)
- [Nikita Tolkachev](https://github.com/n7olkachev)

## Sponsored by

https://websecret.by/

Web agency based in Minsk, Belarus

## License

The MIT License (MIT)
