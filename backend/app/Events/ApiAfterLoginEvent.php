<?php

namespace App\Events;


class ApiAfterLoginEvent
{
    public $user;

    public function __construct(&$user) {
        $this->user = $user;
    }
}
