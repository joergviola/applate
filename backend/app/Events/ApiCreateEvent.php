<?php

namespace App\Events;


class ApiCreateEvent
{
    public $type;
    public $item;

    public function __construct($type, &$item) {
        $this->type = $type;
        $this->item = &$item;
    }
}