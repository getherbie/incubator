<?php

namespace Herbie;

use Herbie\Page;

class Pages implements \IteratorAggregate
{
    protected $items = [];
    protected $selector = null;
    protected static $pages = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->selector = (new Selector(static::CLASS));
    }

    /**
     * Add a menu item.
     * @param MenuItem $item
     */
    public function add(Page $item)
    {
        $route = $item["route"];
        $this->items[$route] = $item;
    }

    public function get($selector)
    {
        $selectorArray = $this->getSelector($selector);

        if (empty($selectorArray)) {

            if (substr($selector, 0, 1) === "@") {
                $selector = "path=" . $selector;
            } else {
                $selector = "route=" . $selector;
            }
        }

        $page = $this->find($selector)->first();
        if (is_null($page)) {
            $page = new NullPage([]);
        }
        return $page;
    }

    public function find($selector)
    {
        $pages = $this::getPages();
        $menuItems = $this->selector->find($selector, $pages);
        return $menuItems;
    }

    public function count($selector = null)
    {
        if (is_null($selector)) {
            return parent::count();
        }
        $pages = $this->find($selector);
        return count($pages);
    }

    public static function create()
    {
        $app = App::instance();
        $files = $app->getFiles("@page", "txt");
        $items = [];
        foreach ($files as $alias) {
            $data = $app->parsePageData($alias);
            $items[] = new Page($data);
        }
        return new static($items);
    }


    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }


    /**
     * @return array
     */
    public static function getPages()
    {
        $app = App::instance();
        $cacheFile = $app->getConfig('site.path') . "/cache/pages.serialized";
        if (!empty(static::$pages)) {
            return static::$pages;
        } elseif (is_file($cacheFile)) {
            static::$pages = unserialize(file_get_contents($cacheFile));
            return static::$pages;
        } else {
            $files = $app->getFiles("@page", "txt");
            foreach ($files as $alias) {
                $data = $app->parsePageData($alias);
                static::$pages[$data["route"]] = new Page($data);
            }
            #file_put_contents($cacheFile, serialize(static::$pages));
            return static::$pages;
        }
    }

}
