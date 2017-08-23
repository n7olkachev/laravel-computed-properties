<?php

namespace N7olkachev\ComputedProperties;

use Illuminate\Database\Query\Expression;

class ModelProxy
{
    protected $model;

    protected $runningInQuery;

    public function __construct($model, $runningInQuery)
    {
        $this->model = $model;
        $this->runningInQuery = $runningInQuery;
    }

    public function __get($key)
    {
        return $this->runningInQuery ? $this->getTableField($key) : $this->getModelAttribute($key);
    }

    protected function getTableField($key)
    {
        return new Expression($this->model->getTable() . '.' . $key);
    }

    protected function getModelAttribute($key)
    {
        return $this->model->$key;
    }
}
