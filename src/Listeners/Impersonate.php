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

use FoF\Impersonate\Events\Impersonated;
use FoF\ModeratorNotes\Command\CreateModeratorNote;
use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Illuminate\Contracts\Events\Dispatcher;

class Impersonate
{
    protected $bus;

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Impersonated::class, [$this, 'addNote']);
    }

    public function addNote(Impersonated $event): void
    {
        $this->bus->dispatch(
            new CreateModeratorNote(
                $event->actor,
                $event->user->id,
                (property_exists($event, 'switchReason') ? $event->switchReason : app('translator')->trans('fof-moderator-notes.api.auto-note'))
            )
        );
        $this->bus->dispatch(
            new CreateModeratorNote(
                $event->actor,
                $event->actor->id,
                app('translator')->trans(
                    'fof-moderator-notes.api.auto-note-actor',
                    [
                        'username' => $event->user->username,
                        'userId'   => $event->user->id,
                        'reason'   => (property_exists($event, 'switchReason') ? $event->switchReason : app('translator')->trans('fof-moderator-notes.api.no_reason_provided')),
                    ]
                )
            )
        );
    }
}
