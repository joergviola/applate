<?php

namespace App\Listeners;

use App\Events\ApiQueryEvent;

class RemovePassword
{
    public function handle(ApiQueryEvent $event) {
        if ($event->type!='user') return;

        unset($event->item->password);
    }
}
