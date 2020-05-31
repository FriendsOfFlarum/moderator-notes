<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Listeners;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\UserSerializer;
use FoF\ModeratorNotes\Model\ModeratorNote;
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

            if ($event->actor->can('viewModeratorNotes', $event->model)) {
                $event->attributes['moderatorNoteCount'] = ModeratorNote::where('user_id', $event->model->id)->count();
            }

            if ($event->actor->hasPermission('user.deleteModeratorNotes')) {
                $event->attributes['canDeleteModeratorNotes'] = $event->actor->hasPermission('user.deleteModeratorNotes');
            }
        }
    }
}
