<?php

namespace App\Events;


class ApiUpdateEvent
{
    public $type;
    public $item;

    public function __construct($type, &$item) {
        $this->type = $type;
        $this->item = &$item;
    }
}