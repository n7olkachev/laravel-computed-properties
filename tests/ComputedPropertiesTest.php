<?php

namespace N7olkachev\ComputedProperties\Test;

use Carbon\Carbon;
use N7olkachev\ComputedProperties\Test\Models\Order;
use N7olkachev\ComputedProperties\Test\Models\Page;

class ComputedPropertiesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $page = Page::create();
        $page->views()->create(['viewed_at' => Carbon::create(2017, 8, 21, 0, 0, 0)]);
        $page->views()->create(['viewed_at' => Carbon::create(2017, 8, 16, 0, 0, 0)]);

        $order = Order::create();
        $order->products()->create(['price' => 50, 'count' => 1]);
        $order->products()->create(['price' => 25, 'count' => 2]);

        $order = Order::create();
        $order->products()->create(['price' => 10, 'count' => 2]);
    }

    /** @test */
    public function it_works_on_model_instance()
    {
        $page = Page::first();
        $this->assertEquals($page->last_view->day, 21);
        $this->assertEquals($page->first_view->day, 16);
    }

    /** @test */
    public function it_works_on_query()
    {
        $page = Page::withComputed('first_view')
            ->groupBy('id')
            ->having('first_view', Carbon::create(2017, 8, 16, 0, 0, 0))
            ->first();

        $this->assertTrue($page->exists);
    }

    /** @test */
    public function it_can_subselect_multiple_properties()
    {
        $page = Page::withComputed(['first_view', 'last_view'])
            ->groupBy('id')
            ->having('first_view', Carbon::create(2017, 8, 16, 0, 0, 0))
            ->having('last_view', Carbon::create(2017, 8, 21, 0, 0, 0))
            ->first();

        $this->assertTrue($page->exists);
    }

    /** @test */
    public function it_can_compute_sum_on_instance()
    {
        $order = Order::first();

        $this->assertEquals($order->sum, 100);
    }

    /** @test */
    public function it_can_handle_sum_subquery()
    {
        $order = Order::withComputed('sum')
            ->having('sum', 100)
            ->groupBy('id')
            ->first();

        $this->assertTrue($order->exists);
    }

    /** @test */
    public function it_can_be_used_with_order_by()
    {
        $order = Order::withComputed('sum')
            ->orderBy('sum', 'asc')
            ->first();

        $this->assertEquals($order->sum, 20);
    }
}