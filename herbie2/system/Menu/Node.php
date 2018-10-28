<?php
/**
 * This file is part of Herbie2.
 *
 * (c) Thomas Breuss <www.tebe.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Herbie2\Menu;

class Node extends \Tebe\Node
{
    /**
     * @return string
     */
    public function __toString()
    {
        $menuItem = $this->getMenuItem();
        return (string) $menuItem->title;
    }

    /**
     * @param Collection $menuCollection
     * @return Node
     */
    public static function buildTree($menuCollection)
    {
        $tree = new static();
        foreach ($menuCollection as $menuItem) {
            $route = $menuItem->getParentRoute();
            $node = $tree->findByRoute($route);
            if ($node) {
                $node->addChild(new static($menuItem));
            }
        }
        return $tree;
    }

    /**
     * @return mixed
     */
    public function getMenuItem()
    {
        return $this->getValue();
    }

    /**
     * @param string $route
     * @return Node|bool
     */
    public function findByRoute($route)
    {
        if (empty($route)) {
            return $this->root();
        }
        $menuItem = $this->getMenuItem();
        if (isset($menuItem) && ($menuItem->route == $route)) {
            return $this;
        }
        foreach ($this->getChildren() as $child) {
            $node = $child->findByRoute($route);
            if ($node) {
                return $node;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Node|bool
     */
    public function findBy($name, $value)
    {
        $menuItem = $this->getMenuItem();
        if (isset($menuItem) && ($menuItem->$name === $value)) {
            return $this;
        }
        foreach ($this->getChildren() as $child) {
            $node = $child->findBy($name, $value);
            if ($node) {
                return $node;
            }
        }
        return false;
    }
}
