<?php

namespace App\Listeners;

use App\Events\ApiUpdateEvent;
use Illuminate\Support\Facades\Hash;

class HashPassword
{
    public function handle(ApiUpdateEvent $event) {
        if ($event->type!='user') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }
}
