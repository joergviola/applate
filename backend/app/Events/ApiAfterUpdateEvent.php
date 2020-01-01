<?php

namespace App\Events;


class ApiAfterUpdateEvent
{
    public $user;
    public $type;
    public $id;
    public $count;
    public $item;
    public $meta;

    public function __construct($user, $type, $id, $count, $item, &$meta) {
        $this->user = $user;
        $this->type = $type;
        $this->id = $id;
        $this->count = $count;
        $this->item = &$item;
        $this->meta = &$meta;
    }
}
