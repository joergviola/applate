<?php

namespace App\Listeners;

use App\Events\ApiBeforeCreateEvent;
use App\Events\ApiAfterReadEvent;
use App\Events\ApiBeforeUpdateEvent;
use Illuminate\Support\Facades\Hash;

class UserEventSubscriber
{

    public function handleQuery(ApiAfterReadEvent $event) {
        if ($event->type!='users') return;

        foreach ($event->items as $user) {
            unset($user->password);
        }
    }

    public function handleUpdate(ApiBeforeUpdateEvent $event) {
        if ($event->type!='users') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }

    public function handleCreate(ApiBeforeCreateEvent $event) {
        if ($event->type!='users') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }
}
