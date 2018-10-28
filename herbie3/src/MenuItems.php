<?php

namespace Herbie;

class MenuItems implements \IteratorAggregate
{
    protected $items = [];
    protected $selector = null;
    protected static $pages = [];

    /**
     * MenuItems constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->selector = (new Selector(static::CLASS));
    }

    /**
     * Add a menu item.
     * @param MenuItem $item
     */
    public function add(MenuItem $item)
    {
        $route = $item["route"];
        $this->items[$route] = $item;
    }

    /**
     * Remove the menu item with the given route.
     * @param string $route
     */
    public function remove($route)
    {
        if (isset($this->items[$route])) {
            unset($this->items[$route]);
        }
    }

    /**
     * @param array|string $selector
     * @return MenuItems
     */
    public function find($selector)
    {
        $pages = $this::getPages();
        $menuItems = $this->selector->find($selector, $pages);
        return $menuItems;
    }

    /**
     * @param array|string $selector
     * @return MenuItem|null
     */
    public function get($selector)
    {
        $pages = $this::getPages();
        $menuItem = $this->selector->get($selector, $pages);
        return $menuItem;
    }

    /**
     * @return MenuItem|null
     */
    public function root()
    {
        $pages = $this::getPages();
        $menuItem = $this->selector->get("parent=__root__", $pages);
        return $menuItem;
    }

    /**
     * @return MenuItem|null
     */
    public function first()
    {
        $item = reset($this->items);
        return $item ? $item : null;
    }

    /**
     * @return MenuItem|null
     */
    public function last()
    {
        $item = end($this->items);
        return $item ? $item : null;
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
        $cacheFile = $app->getConfig('site.path') . "/cache/menuitems.serialized";
        if (!empty(static::$pages)) {
            return static::$pages;
        } elseif (is_file($cacheFile)) {
            static::$pages = unserialize(file_get_contents($cacheFile));
            return static::$pages;
        } else {
            $files = $app->getFiles("@page", "txt");
            $fields = $app->getConfig('pages.meta_fields');
            foreach ($files as $alias) {
                $data = $app->parsePageData($alias, $fields);
                static::$pages[$data["route"]] = new MenuItem($data);
            }
            #file_put_contents($cacheFile, serialize(static::$pages));
            #echo"<pre>";print_r(static::$pages);echo"</pre>";
            return static::$pages;
        }
    }

}
