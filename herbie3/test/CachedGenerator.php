<?php

namespace Herbie;

class CachedGenerator {
    protected $cache = [];
    protected $generator = null;

    public function __construct($generator) {
        $this->generator = $generator;
    }

    public function generator() {
        foreach($this->cache as $item) yield $item;

        while( $this->generator->valid() ) {
            $this->cache[] = $current = $this->generator->current();
            $this->generator->next();
            yield $current;
        }
    }
}
