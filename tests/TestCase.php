<?php

namespace N7olkachev\ComputedProperties\Test;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory().'/database.sqlite',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', '6rE9Nz372GRbeMATftriyQjrpF7DcOQm');
    }

    protected function setUpDatabase()
    {
        $this->resetDatabase();
        $this->createTables();
    }

    protected function resetDatabase()
    {
        file_put_contents($this->getTempDirectory().'/database.sqlite', null);
    }

    public function getTempDirectory(): string
    {
        return __DIR__.'/temp';
    }

    protected function createTables()
    {
        $this->createPagesTable();
        $this->createPageViewsTable();
        $this->createOrdersTable();
        $this->createOrderProductsTable();
    }

    protected function createPagesTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    protected function createPageViewsTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('page_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned()->nullable();
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->timestamp('viewed_at');
        });
    }

    protected function createOrdersTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    protected function createOrderProductsTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->integer('price');
            $table->integer('count');
            $table->timestamps();
        });
    }
}