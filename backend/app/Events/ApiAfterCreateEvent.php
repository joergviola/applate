<?php

namespace App\Events;


class ApiAfterCreateEvent
{
    public $user;
    public $type;
    public $id;
    public $item;
    public $meta;

    public function __construct($user, $type, $id, &$item, &$meta) {
        $this->user = $user;
        $this->type = $type;
        $this->id = $id;
        $this->item = &$item;
        $this->meta = &$meta;
    }
}
