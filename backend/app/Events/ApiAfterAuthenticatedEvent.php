<?php

namespace App\Events;


class ApiAfterAuthenticatedEvent
{
    public $user;

    public function __construct(&$user) {
        $this->user = $user;
    }
}
