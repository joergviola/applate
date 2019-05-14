<?php

namespace App\Listeners;

use App\API;
use App\Events\ApiCreateEvent;
use App\Events\ApiQueryEvent;
use App\Events\ApiUpdateEvent;
use Illuminate\Database\QueryException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Hash;

class NotificationsEventSubscriber
{

    public function handleUpdate(ApiUpdateEvent $event) {
        $this->notify($event->user, $event->type, $event->id, 'U');
    }

    public function handleCreate(ApiCreateEvent $event) {
        $this->notify($event->user, $event->type, $event->id, 'C');
    }

    private function notify($user, $type, $id, $operation) {
        $listeners = API::provider('listen')
            ->where('type', $type)
            ->where('item_id', $id)
            ->get();
        foreach ($listeners as $listener) {
            if (strstr($listener->operation, $operation)!==FALSE) {
                try {
                    API::provider('notification')->insert([
                        'client_id' => $user['client_id'],
                        'listener_id' => $listener->listener_id,
                        'user_id' => $user['id'],
                        'type' => $type,
                        'item_id' => $id,
                        'operation' => $operation,
                    ]);
                } catch (QueryException $e) {
                    // Notification already there, doesn't matter...
                }
            }
        }
    }
}
