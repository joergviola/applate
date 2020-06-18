<?php

namespace App\Listeners;

use App\API;
use App\Events\ApiAfterAuthenticatedEvent;

class AuthenticatedEventSubscriber
{

    public function handleQuery(ApiAfterAuthenticatedEvent $event) {
        $user = $event->user;
        $project_ids = API::provider('allocation')
            ->where('user_id', $user->id)
            ->pluck('project_id')
            ->toArray();
        $user->projects = implode(',', $project_ids);
    }
}
