<?php

namespace FoF\ModeratorNotes\Listeners;


use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\UserSerializer;
use Illuminate\Contracts\Events\Dispatcher;

class Permissions
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Serializing::class, [$this, 'addPermission']);
    }

    public function addPermission(Serializing $event)
    {
        if ($event->isSerializer(UserSerializer::class)) {
            $event->attributes['canViewModeratorNotes'] = $event
                ->actor
                ->can('viewModeratorNotes', $event->model);
                
            $event->attributes['canCreateModeratorNotes'] = $event
                ->actor
                ->can('createModeratorNotes', $event->model);
        }
    }
}
