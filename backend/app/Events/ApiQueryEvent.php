<?php

namespace App\Events;


class ApiQueryEvent
{
    public $type;
    public $item;

    public function __construct($type, &$item) {
        $this->type = $type;
        $this->item = &$item;
    }
}