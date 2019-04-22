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
        if ($event->type!='user') return;

        unset($event->item->password);
    }


    public function handleUpdate(ApiUpdateEvent $event) {
        if ($event->type!='user') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }

    public function handleCreate(ApiCreateEvent $event) {
        if ($event->type!='user') return;

        if (!empty($event->item['password'])) {
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }

    public function subscribe(Dispatcher $events) {
        $events->listen('App\Events\ApiCreateEvent', 'App\Listeners\UserEventSubscriber@handleCreate');
        $events->listen('App\Events\ApiQUERYEvent', 'App\Listeners\UserEventSubscriber@handleQuery');
    }
}
