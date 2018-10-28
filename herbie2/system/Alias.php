<?php

/**
 * This file is part of Herbie.
 *
 * (c) Thomas Breuss <www.tebe.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Herbie2;

class Alias
{

    /**
     * @var array
     */
    protected static $aliases = [];

    /**
     * @param array $aliases
     */
    public static function init(array $aliases = [])
    {
        static::$aliases = [];
        foreach ($aliases as $alias => $path) {
            static::set($alias, $path);
        }
    }

    /**
     * @param string $alias
     * @param string $path
     * @throws \Exception
     */
    public static function set($alias, $path)
    {
        if (array_key_exists($alias, static::$aliases)) {
            throw new \Exception("Alias {$alias} already set, use update instead.");
        }
        static::$aliases[$alias] = static::rtrim($path);
    }

    /**
     * @param string $alias
     * @return string
     */
    public static function get($alias)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }
        return strtr($alias, static::$aliases);
    }

    /**
     * @param string $alias
     * @param string $path
     * @throws \Exception
     */
    public static function update($alias, $path)
    {
        if (array_key_exists($alias, static::$aliases)) {
            static::$aliases[$alias] = static::rtrim($path);
        } else {
            throw new \Exception("Alias {$alias} not exists, use set instead.");
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private static function rtrim($path)
    {
        return rtrim($path, '/');
    }
}
