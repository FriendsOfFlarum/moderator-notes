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

    public function addPermission(Serializing $serializingEvent)
    {
        if ($serializingEvent->isSerializer(UserSerializer::class)) {
            $serializingEvent->attributes['canViewModeratorNotes'] = $serializingEvent
                ->actor
                ->can('viewModeratorNotes', $serializingEvent->model);
            $serializingEvent->attributes['canCreateModeratorNotes'] = $serializingEvent
                ->actor
                ->can('createModeratorNotes', $serializingEvent->model);
        }
    }
}
