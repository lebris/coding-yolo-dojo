<?php

declare(strict_types = 1);

namespace Dojo\Domain;

class Cart
{
    private
        $items = [];

    public function add($item)
    {
        if(! array_key_exists($item, $this->items))
        {
            $this->items[$item] = 0;
        }

        $this->items[$item]++;

        return $this;
    }
}
