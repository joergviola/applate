<?php

namespace App\Events;


class ApiUpdateEvent
{
    public $user;
    public $type;
    public $id;
    public $item;

    public function __construct($user, $type, $id, &$item) {
        $this->user = $user;
        $this->type = $type;
        $this->id = $id;
        $this->item = &$item;
    }
}