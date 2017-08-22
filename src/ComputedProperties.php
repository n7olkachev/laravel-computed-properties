<?php

namespace N7olkachev\ComputedProperties;

use Illuminate\Database\Eloquent\Builder;

trait ComputedProperties
{
    public static function bootComputedProperties()
    {
        Builder::macro('withComputed', function ($properties) {
            collect($properties)->each(function ($property) {
                $query = $this->model->callComputedProperty($property, true)->toBase();

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
            $result = $this->callComputedProperty($key, false);
            $this->attributes[$key] = array_first($result->first()->getAttributes());
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

    public function callComputedProperty($property, $inQuery)
    {
        $method = $this->computedPropertyMethodName($property);

        return $this->$method(new ModelProxy($this, $inQuery));
    }
}