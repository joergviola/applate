<?php

namespace App\Listeners;

use App\Events\ApiCreateEvent;
use App\Events\ApiQueryEvent;
use App\Events\ApiUpdateEvent;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Hash;

class UserEventSubscriber
{

    public function handleQuery(ApiQueryEvent $event) {
        if ($event->type!='users') return;

        foreach ($event->items as $user) {
            unset($user->password);
        }
    }

    public function handleUpdate(ApiUpdateEvent $event) {
        if ($event->type!='users') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }

    public function handleCreate(ApiCreateEvent $event) {
        if ($event->type!='users') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }
}
