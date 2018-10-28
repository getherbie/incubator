<?php

namespace Herbie;

/**
 * Class MenuItem
 * @package Herbie
 * @see https://processwire.com/api/variables/page/
 */
class MenuItem implements \ArrayAccess
{
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param array|string $selector
     * @return MenuItem|null
     */
    public function parent($selector = "")
    {
        if ($this["parent"] == "__root__") {
            return null;
        }
        $pages = $this->getPages();
        $selectors = Selector::mergeSelectors("route=" . $this["parent"], $selector);
        $items = $this->selector()->find($selectors, $pages);
        return $items->first();
    }

    /**
     * @param array|string $selector
     * @return MenuItems
     */
    public function parents($selector = "")
    {
        $routes = explode("/", $this["route"]);
        array_unshift($routes, "");
        array_pop($routes);
        $pages = $this->getPages();
        $menuItems = new MenuItems();
        foreach ($routes as $route) {
            if (isset($pages[$route])) {
                $menuItems->add($pages[$route]);
            }
        }
        if (strlen($selector)) {
            $menuItems = $this->selector()->find($selector, $menuItems);
        }
        return $menuItems;
    }

    /**
     * @param array|string $selector
     * @return MenuItems
     */
    public function children($selector = "")
    {
        $pages = $this->getPages();
        $selectors = Selector::mergeSelectors("parent=" . $this["route"], $selector);
        $items = $this->selector()->find($selectors, $pages);
        return $items;
    }

    /**
     * @param array|string $selector
     * @return MenuItem|null
     */
    public function child($selector = "")
    {
        $items = $this->children($selector);
        return $items->first();
    }

    /**
     * @param array|string $selector
     * @return MenuItems
     */
    public function siblings($selector = "")
    {
        $pages = $this->getPages();
        $selectors = Selector::mergeSelectors("parent=" . $this["parent"], $selector);
        $items = $this->selector()->find($selectors, $pages);
        $items->remove($this["route"]);
        return $items;
    }

    /**
     * @param array|string $selector
     * @return MenuItem|null
     */
    public function prev($selector = "")
    {
        $route = $this["route"];
        $pages = $this->getPages();
        $prev = false;
        foreach (array_reverse($pages) as $page) {
            if ($page["route"] == $route) {
                $prev = true;
                continue;
            }
            if ($prev) {
                if (strlen($selector)) {
                    $items = [$route => $page];
                    $item = $this->selector()->get($selector, $items);
                    if ($item) {
                        return $item;
                    }
                } else {
                    return $page;
                }
            }
        }
        return null;
    }

    /**
     * @param array|string $selector
     * @return MenuItem|null
     */
    public function next($selector = "")
    {
        $route = $this["route"];
        $next = false;
        $pages = $this->getPages();
        foreach ($pages as $page) {
            if ($page["route"] == $route) {
                $next = true;
                continue;
            }
            if ($next) {
                if (strlen($selector)) {
                    $items = [$route => $page];
                    $item = $this->selector()->get($selector, $items);
                    if ($item) {
                        return $item;
                    }
                } else {
                    return $page;
                }
            }
        }
        return null;
    }

    /**
     * @param array|string $selector
     * @return MenuItems
     */
    public function find($selector = "")
    {
        $pages = $this->getPages();
        $selectors = Selector::mergeSelectors("parent^=" . $this["route"], $selector);
        $items = $this->selector()->find($selectors, $pages);
        return $items;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param mixed $name
     */
    public function remove($name)
    {
        unset($this->data[$name]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @return Selector
     */
    protected function selector()
    {
        $selector = new Selector(
            MenuItems::CLASS
        );
        return $selector;
    }

    /**
     * @return array
     */
    private function getPages()
    {
        return MenuItems::getPages();
    }

}
