<?php

namespace App\Events;


class ApiBeforeDeleteEvent
{
    public $user;
    public $type;
    public $id;

    public function __construct($user, $type, $id) {
        $this->user = $user;
        $this->type = $type;
        $this->id = $id;
    }
}