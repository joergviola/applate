<?php

namespace App\Listeners;

use App\API;
use App\Events\ApiAfterCreateEvent;
use App\Events\ApiAfterUpdateEvent;
use App\Events\ApiBeforeDeleteEvent;

class VersioningEventSubscriber
{

    public function handleUpdate(ApiAfterUpdateEvent $event) {
        $item = API::provider($event->type)->find($event->id);
        API::provider('log')->insert([
            'client_id' => $event->user['client_id'],
            'created_at' => new \DateTime(),
            'user_id' => $event->user['id'],
            'type' => $event->type,
            'item_id' => $event->id,
            'operation' => 'U',
            'content' => json_encode($item),
        ]);
    }

    public function handleCreate(ApiAfterCreateEvent $event) {
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

    public function handleDelete(ApiBeforeDeleteEvent $event) {
        $item = API::provider($event->type)->find($event->id);
        API::provider('log')->insert([
            'client_id' => $event->user['client_id'],
            'created_at' => new \DateTime(),
            'user_id' => $event->user['id'],
            'type' => $event->type,
            'item_id' => $event->id,
            'operation' => 'D',
            'content' => json_encode($item),
        ]);
    }
}
