<?php

namespace App\Events;


class ApiQueryEvent
{
    public $type;
    public $items;

    public function __construct($type, &$items) {
        $this->type = $type;
        $this->items = &$items;
    }
}