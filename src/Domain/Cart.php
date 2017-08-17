<?php

declare(strict_types = 1);

namespace Dojo\Domain;

class Cart implements \IteratorAggregate
{
    private
        $items = [];

    public function add(Item $item)
    {
        if(! array_key_exists($item, $this->items))
        {
            $this->items[$item] = 0;
        }

        $this->items[$item]++;

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
