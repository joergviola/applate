<?php

namespace App\Listeners;

use App\Events\ApiAfterReadEvent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DocumentEventSubscriber
{

    public function handleQuery(ApiAfterReadEvent $event) {
        if ($event->type!='document') return;

        foreach ($event->items as &$item) {
            $type = $item->type;
            $id = $item->item_id;
            $path = $item->path;
            $name = $item->name;
            $filepath = "$type/$id/$path/$name";
            if (env('FILESYSTEM', 'public')=="s3") {
                $item->url = $this->signedUrl($filepath);
            } else {
                $item->url = URL::to(Storage::url($filepath));
            }
        }
    }

    private function signedUrl($filepath) {
        $s3 = Storage::disk(env('FILESYSTEM', 'public'));
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = env("AWS_URL_EXPIRY", "+2 minutes");

        $command = $client->getCommand('GetObject', [
            'Bucket' => Config::get('filesystems.disks.s3.bucket'),
            'Key'    => $filepath
        ]);

        $request = $client->createPresignedRequest($command, $expiry);

        return (string) $request->getUri();
    }
}
