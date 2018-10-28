<?php

namespace Herbie;

class Files implements \IteratorAggregate
{

    protected $items;
    protected $selector;

    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->selector = (new Selector(static::CLASS));
    }

    public function add($item)
    {
        $this->items[] = $item;
    }

    public function find($selector = "")
    {
        $files = $this->getFiles();
        $files = $this->selector->find($selector, $files);
        return $files;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public static function getFiles($alias = "@web")
    {
        $app = App::instance();
        $files = $app->getFiles($alias, "");
        $items = [];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        foreach ($files as $alias) {
            $path = $app->getPathOfAlias($alias);
            $info = pathinfo($path);
            /*
            $stat = stat($path);
            echo"<pre>";
            print_r($info);
            print_r($stat);
            echo"</pre>";
            */
            $info["mime"] = $finfo->file($path);
            $info["dirname"] = dirname($alias);
            $info["size"] = filesize($path);
            $items[] = new File($info);
        }
        return $items;
    }

}

