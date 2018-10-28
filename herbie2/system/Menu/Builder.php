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

use Herbie2\Filesystem\FileFilterCallback;
use Herbie2\Filesystem\RecursiveDirectoryIterator;
use Herbie2\Filesystem\SortableIterator;
use Herbie2\Filesystem\PageFileInfo;
use Herbie2\Filesystem\PageFileObject;
use Herbie2\Filesystem\RecursiveIteratorIterator;

class Builder
{

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $paths;

    /**
     * @var array
     */
    protected $extensions;

    /**
     * @param array $paths
     * @param array $extensions
     */
    public function __construct(array $paths, array $extensions)
    {
        $this->paths = $paths;
        $this->extensions = $extensions;
    }

    /**
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return void
     */
    public function unsetCache()
    {
        $this->cache = null;
    }

    /**
     * @return Collection
     */
    public function buildCollection()
    {
        $collection = $this->restoreCollection();

        if (!$collection->fromCache) {

            PageFileInfo::$alias = $this->paths;
            foreach ($this->paths as $alias => $path) {

                $indexFiles = [];

                foreach ($this->getIterator($alias) as $pageFileInfo) {

                    /** @var $pageFileInfo PageFileInfo */

                    if ($pageFileInfo->isDir()) {
                        // index file as describer for parent folder
                        foreach ($pageFileInfo->getIndexFiles($pageFileInfo) as $pageFileInfo) {
                            $indexFiles[] = $pageFileInfo->getRealPath();
                            $data = $pageFileInfo->getFrontmatter();
                            $collection->addItem(new Item($data));
                            break;
                        }
                    } else {

                        // other files
                        if (in_array($pageFileInfo->getRealPath(), $indexFiles)) {
                            continue;
                        }

                        $data = $pageFileInfo->getFrontmatter();
                        $collection->addItem(new Item($data));
                        
                    }

                }

            }

            $this->storeCollection($collection);

        }

        #echo"<pre>";print_r($collection);echo"</pre>";

        return $collection;
    }

    /**
     * @return Collection
     */
    private function restoreCollection()
    {
        if (is_null($this->cache)) {
            return new Collection();
        }
        $collection = $this->cache->get(__CLASS__);
        if ($collection === false) {
            return new Collection();
        }
        return $collection;
    }

    /**
     * @param $collection
     * @return bool
     */
    private function storeCollection($collection)
    {
        if (is_null($this->cache)) {
            return false;
        }
        $collection->fromCache = true;
        return $this->cache->set(__CLASS__, $collection);
    }


    /**
     * @param string $path
     * @return SortableIterator
     */
    protected function getIterator($path)
    {
        $directoryIterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);

        $filterCallback = [new FileFilterCallback($this->extensions), 'filter'];
        $filterIterator = new \RecursiveCallbackFilterIterator($directoryIterator, $filterCallback);

        $iteratorIterator = new \RecursiveIteratorIterator($filterIterator, \RecursiveIteratorIterator::SELF_FIRST);

        return new SortableIterator($iteratorIterator, SortableIterator::SORT_BY_NAME);
    }

}
