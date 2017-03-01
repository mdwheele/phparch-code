<?php

namespace Sample\Support;

use ArrayAccess;

class AttributeStore implements ArrayAccess
{
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function has($key)
    {
        if (is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $this->items)) {
            return true;
        }

        $cursor = $this->items;

        foreach (explode('.', $key) as $segment) {
            if (array_key_exists($segment, $cursor)) {
                $cursor = $cursor[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    public function get($key, $default = null)
    {
        if (is_null($key)) {
            return $this->all();
        }

        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        $cursor = $this->items;
        foreach (explode('.', $key) as $segment) {
            if (array_key_exists($segment, $cursor)) {
                $cursor = $cursor[$segment];
            } else {
                return $default;
            }
        }

        return $cursor;
    }

    public function set($key, $value = null)
    {
        if (is_null($key)) {
            throw new Exception('Cannot set value in AttributeStore with null key.');
        }

        $keys = explode('.', $key);

        $cursor = &$this->items;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($cursor[$key]) || ! is_array($cursor[$key])) {
                $cursor[$key] = [];
            }

            $cursor = &$cursor[$key];
        }

        if (is_null($value)) {
            unset($cursor[array_shift($keys)]);
        } else {
            $cursor[array_shift($keys)] = $value;
        }
    }

    public function all()
    {
        return $this->items;
    }

    public function offsetExists($key)
    {
        return $this->has($key);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

}