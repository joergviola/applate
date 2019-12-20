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
            $this->checkOldPassword($event);
            unset($event->item['old_password']);
            $event->item['password'] = Hash::make($event->item['password']);
        } else {
            unset($event->item['password']);
        }
    }

    private function checkOldPassword($event) {
        if (isset($event->item['old_password'])) {
            $hash = Hash::make($event->item['old_password']);
            if ($hash==Auth::user()->password) {
                return;
            }
        }
        throw new PermissionException("Unable to change password");
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
