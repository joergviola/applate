<?php

namespace App\Events;


class ApiAfterUpdateEvent
{
    public $user;
    public $type;
    public $id;
    public $count;

    public function __construct($user, $type, $id, $count) {
        $this->user = $user;
        $this->type = $type;
        $this->id = $id;
        $this->count = $count;
    }
}