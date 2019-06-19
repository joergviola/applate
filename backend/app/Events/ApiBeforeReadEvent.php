<?php

namespace App\Events;


class ApiBeforeReadEvent
{
    public $user;
    public $type;
    public $query;

    public function __construct($user, $type, &$query) {
        $this->user = $user;
        $this->type = $type;
        $this->query = &$query;
    }
}