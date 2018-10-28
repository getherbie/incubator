<?php

/**
 * This file is part of Herbie2.
 *
 * (c) Thomas Breuss <www.tebe.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Herbie;

class Menu extends HArray
{

    public $fromCache;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $route = $item->getRoute();
        $this->items[$route] = $item;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param string $route
     * @return Item|null
     */
    public function getItem($route)
    {
        return isset($this->items[$route]) ? $this->items[$route] : null;
    }

    /**
     * @return Item
     */
    public function getRandom()
    {
        $routes = array_keys($this->items);
        $index = mt_rand(0, $this->count() - 1);
        $route = $routes[$index];
        return $this->items[$route];
    }

    /**
     * Run a filter over each of the items.
     *
     * @param callable|null $key
     * @param mixed $value
     * @return static
     */
    public function filter($key = null, $value = null)
    {
        if (is_callable($key)) {
            return new static(array_filter($this->items, $key));
        }
        if (is_string($key) && is_scalar($value)) {
            return new static(array_filter($this->items, function ($val) use ($key, $value) {
                if ($val->{$key} == $value) {
                    return true;
                }
                return false;
            }));
        }
        return new static(array_filter($this->items));
    }

    /**
     * Shuffle the items in the collection.
     *
     * @return static
     */
    public function shuffle()
    {
        $items = $this->items;
        shuffle($items);
        return new static($items);
    }

    public function flatten()
    {
        return $this->items;
    }

    public static function create()
    {
        static $static;
        if (is_null($static)) {
            $menu = [];
            $app = App::instance();
            $files = $app->getFiles('@page', 'txt');
            $metaFields = $app->getConfig('pages.meta_fields');
            foreach ($files as $alias) {
                $meta = $app->parsePageData($alias, $metaFields);
                $key = $meta['route'];
                $menu[$key] = $meta;
            }
            $static = new static($menu);
        }
        return $static;
    }

}
