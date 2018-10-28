<?php

namespace Herbie;

use Parsedown;

class Page implements \ArrayAccess {

    protected $data = [];

    protected $config = [];

    public function __construct($data)
    {
        $this->data = $data;
        $this->config = [
            "content" => [
                "format" => "markdown",
            ],
            "content_boxes" => [
                "format" => "markdown",
            ],
            "content_footer" => [
                "format" => "markdown",
            ]
        ];
    }

    public function raw($key, $default = "")
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    public function get($key, $default = "")
    {
        $raw = $this->raw($key, $default);
        return $this->format($raw, $key);
    }

    public function setConfig(array $config)
    {
        static::$config = $config;
    }

    protected function format($value, $key)
    {
        if (isset($this->config[$key]["format"])) {
            if ($this->config[$key]["format"] === "markdown") {

                App::instance()->triggerEvent('onContentRendering', [&$value]);

                $parser = new \ParsedownExtra();
                $parser->setUrlsLinked(false);
                return $parser->text($value);
            }
        }
        return $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \LogicException
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        return $this->offsetExists($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            $this->offsetSet($name, $value);
        }
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

}
