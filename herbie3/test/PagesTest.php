<?php

namespace Herbie;

class PagesTest extends HArray
{

    public static $parents = [];
    public static $children = [];
    public static $tree = [];
    public static $routes = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
        if (empty(static::$routes)) {
            $this->loadPages();
        }
    }

    public function loadFiles()
    {
        $app = App::instance();
        $files = $app->getFiles('@page', 'txt');
        foreach ($files as $alias) {
            $data = $app->parsePageData($alias);
            #static::$pages[] = new Page($data);
        }
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

        #$this->loadFiles();
        $page = $this->find($selector)->first();
        if (is_null($page)) {
            $page = new NullPage([]);
        }
        return $page;
    }

    public function find($selector)
    {
        /*if (is_callable($selector)) {
            return new static(array_filter($this->items, $selector));
        }*/

        $selectorArray = $this->getSelector($selector);

        if (empty($selectorArray)) {
            return $this;
        }

        list($field, $value, $function) = $selectorArray;

        $this->items = [];
        foreach (static::$routes as $route => $data) {
            if (!isset($data[$field])) continue;
            $result = call_user_func_array($function, [$data[$field], $value]);
            if ($result) {
                $this->items[] = new PageTest($data);
            }
        }
        return $this;
    }

    public function count($selector = null)
    {
        if (is_null($selector)) {
            return parent::count();
        }
        #$this->loadFiles();
        $pages = $this->find($selector);
        return count($pages);
    }

    public function loadPages()
    {
        $app = App::instance();
        $files = $app->getFiles("@page", "txt");
        static::$routes = [];
        foreach ($files as $alias) {
            $data = $app->parsePageData($alias, ["title", "route", "path", "parent"]);
            static::$routes[$data["route"]] = $data;
            static::$tree[$data["parent"]][$data["route"]] = $data;
            static::$children[$data["parent"]][] = $data["route"];
            static::$parents[$data["route"]] = $data["parent"];
        }

        #echo"<pre>";print_r(static::$children);echo"</pre>";
        #echo"<pre>";print_r(static::$parents);echo"</pre>";
    }

    public static function create()
    {
        return new static();

        $app = App::instance();
        $files = $app->getFiles("@page", "txt");
        $items = [];
        foreach ($files as $alias) {
            $data = $app->parsePageData($alias, ["route", "path"]);
            $items[] = new PageTest($data);
        }

        return new static($items);
    }

}
