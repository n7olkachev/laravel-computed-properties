<?php

namespace N7olkachev\ComputedProperties;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait ComputedProperties
{
    public static function bootComputedProperties()
    {
        Builder::macro('withComputed', function ($properties) {
            collect($properties)->each(function ($property) {
                if (!$this->model->hasComputedProperty($property)) {
                    throw new \InvalidArgumentException("Computed property [$property] does not exist");
                }

                $query = $this->model->callComputedProperty($property, true);

                if (is_null($this->query->columns)) {
                    $this->query->select([$this->query->from.'.*']);
                }

                $this->selectSub($query, $property);
            });

            return $this;
        });
    }

    public function __get($key)
    {
        if (!array_key_exists($key, $this->attributes) && $this->hasComputedProperty($key)) {
            $query = $this->callComputedProperty($key, false);
            $result = (array) $query->first();
            $this->attributes[$key] = array_first($result);
        }

        return parent::__get($key);
    }

    public function hasComputedProperty($property)
    {
        return method_exists($this, $this->computedPropertyMethodName($property));
    }

    public function computedPropertyMethodName($property)
    {
        return 'computed' . ucfirst(camel_case($property));
    }

    public function callComputedProperty($property, $runningInQuery)
    {
        $method = $this->computedPropertyMethodName($property);
        $query = $this->$method(new ModelProxy($this, $runningInQuery));

        if ($query instanceof Builder) {
            $query = $query->toBase();
        }

        if (!$query instanceof QueryBuilder) {
            throw new \UnexpectedValueException("Computed property must return EloquentBuilder or QueryBuilder instance");
        }

        return $query;
    }
}