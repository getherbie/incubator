<?php

namespace Herbie;

abstract class HArray implements \IteratorAggregate, \ArrayAccess, \Countable {

    public static $routes = [];

    protected $items = [];
    public function getStaticArray()
    {
        return static::$routes;
    }
    
    public function getIterator() {
        return new \ArrayIterator($this->items);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    public function count() {
        return count($this->items);
    }

    /**
     * Find and return all menu items matching the given selector string.
     *
     * =   Equal to
     * !=  Not equal to
     * <   Less than
     * >   Greater than
     * <=  Less than or equal to
     * >=  Greater than or equal to
     * *=  Contains the exact word or phrase
     * ~=  Contains all the words
     * ^=  Contains the exact word or phrase at the beginning of the field
     * $=  Contains the exact word or phrase at the end of the field
     * &   Bitwise and
     *
     * @param string $selector
     * @return $this|static
     */
    public function find($selector)
    {
        if (is_callable($selector)) {
            return new static(array_filter($this->items, $selector));
        }

        $selectorArray = $this->getSelector($selector);

        if (empty($selectorArray)) {
            return $this;
        }

        list($field, $value, $function) = $selectorArray;

        $items = [];
        foreach ($this->items as $key => $item) {
            if (!isset($item[$field])) continue;
            $result = call_user_func_array($function, [$item[$field], $value]);
            if ($result) {
                $items[] = $item;
            }
        }

        return new static($items);
    }

    protected function getSelector($selector)
    {
        $selector = trim($selector);

        $selectors = [
            "!=" => function ($a, $b) {
                return $a != $b;
            },
            ">=" => function ($a, $b) {
                return $a >= $b;
            },
            "<=" => function ($a, $b) {
                return $a <= $b;
            },
            "*=" => function ($a, $b) {
                return stripos($a, $b) !== false;
            },
            "^=" => function ($a, $b) {
                return stripos(trim($a), $b) === 0;
            },
            "~=" => function ($a, $b) {
                $hasAll = true;
                $words = preg_split('/[-\s]/', $b, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($words as $key => $word) if (!preg_match('/\b' . preg_quote($word) . '\b/i', $a)) {
                    $hasAll = false;
                    break;
                }
                return $hasAll;
            },
            "$=" => function ($value1, $value2) {
                $value2 = trim($value2);
                $value1 = substr($value1, -1 * strlen($value2));
                return strcasecmp($value1, $value2) == 0;
            },
            "&" => function ($value1, $value2) {
                return ((int)$value1) & ((int)$value2);
            },
            ">" => function ($a, $b) {
                return $a > $b;
            },
            "<" => function ($a, $b) {
                return $a < $b;
            },
            "=" => function ($a, $b) {
                return $a == $b;
            },
        ];

        $field = "";
        $value = "";
        $operator = "";

        foreach (array_keys($selectors) as $op) {
            $pos = stripos($selector, $op);
            if ($pos !== false) {
                $field = substr($selector, 0, $pos);
                $value = substr($selector, $pos + strlen($op));
                $operator = $op;
                break;
            }
        }

        if (empty($operator)) {
            return [];
        }

        return [
            $field,
            $value,
            $selectors[$operator]
        ];
    }

    /**
     * @param callable|string $sort
     * @return static
     */
    public function sort($sort)
    {
        $items = $this->items;

        if (is_callable($sort)) {
            uasort($items, $sort);
            return new static($items);
        }

        $field = is_string($sort) ? $sort : 'title';

        $direction = "asc";
        if (substr($field, 0, 1) === "-") {
            $field = substr($field, 1);
            $direction = "desc";
        }

        uasort($items, function ($a, $b) use ($field, $direction) {
            if ($a[$field] == $b[$field]) {
                return 0;
            }
            if ($direction == 'asc') {
                return ($a[$field] < $b[$field]) ? -1 : 1;
            } else {
                return ($b[$field] < $a[$field]) ? -1 : 1;
            }
        });

        return new static($items);
    }

    public function limit($limit, $start = 0)
    {
        return new static(array_slice($this->items, $start, $limit));
    }

    public function first()
    {
        if (count($this->items) > 0) {
            return reset($this->items);
        }
        return null;
    }

}