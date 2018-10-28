<?php

namespace Herbie2\Model;

use Herbie2\Alias;
use Herbie2\Filesystem\PageFileInfo;

class Page {

    protected $data = [];
    protected $segments = [];

    public function __construct($data, $segments)
    {
        $this->data = $data;
        $this->segments = $segments;
    }

    public function getData($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function getSegment($key) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function render($segment)
    {

    }

    /**
     * @param string $path
     * @return static
     */
    public static function findOneByPath($path)
    {
        $info = new PageFileInfo($path);
        $data = $info->getRawData();
        return new static(
            $data[0],
            $data[1]
        );
    }

}
