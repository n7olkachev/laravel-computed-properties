<?php

namespace N7olkachev\ComputedProperties;

class ModelProxy
{
    protected $model;

    protected $inQuery;

    public function __construct($model, $inQuery)
    {
        $this->model = $model;
        $this->inQuery = $inQuery;
    }

    public function __get($key)
    {
        if ($this->inQuery) {
            return \DB::raw($this->model->getTable() . '.' . $key);
        }

        return $this->model->$key;
    }
}
