<?php

namespace App\Listeners;

use App\API;
use App\Events\ApiCreateEvent;
use App\Events\ApiQueryEvent;
use App\Events\ApiUpdateEvent;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Hash;

class VersioningEventSubscriber
{

    public function handleUpdate(ApiUpdateEvent $event) {
        API::provider('log')->insert([
            'client_id' => $event->user['client_id'],
            'created_at' => new \DateTime(),
            'user_id' => $event->user['id'],
            'type' => $event->type,
            'item_id' => $event->id,
            'operation' => 'U',
            'content' => json_encode($event->item),
        ]);
    }

    public function handleCreate(ApiCreateEvent $event) {
        API::provider('log')->insert([
            'client_id' => $event->user['client_id'],
            'created_at' => new \DateTime(),
            'user_id' => $event->user['id'],
            'type' => $event->type,
            'item_id' => $event->id,
            'operation' => 'C',
            'content' => json_encode($event->item),
        ]);
    }
}
