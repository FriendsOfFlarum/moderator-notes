<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Listeners;

use FoF\Impersonate\Events\Impersonated;
use FoF\ModeratorNotes\Command\CreateModeratorNote;
use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Symfony\Contracts\Translation\TranslatorInterface;

class Impersonate
{
    public function __construct(protected Bus $bus, protected TranslatorInterface $translator)
    {
    }

    public function handle(Impersonated $event): void
    {
        // Leave moderator note on impersonate subject
        $this->bus->dispatch(
            new CreateModeratorNote(
                $event->actor,
                $event->user->id,
                $this->translator->trans(
                    'fof-moderator-notes.api.auto_note',
                    [
                        'reason' => (property_exists($event, 'switchReason') &&
                            $event->switchReason !== ''
                            ? $event->switchReason
                            : $this->translator->trans('fof-moderator-notes.api.no_reason_provided')),
                    ]
                )
            )
        );

        // Leave moderator note on impersonate actor
        $this->bus->dispatch(
            new CreateModeratorNote(
                $event->actor,
                $event->actor->id,
                $this->translator->trans(
                    'fof-moderator-notes.api.auto_note_actor',
                    [
                        'username' => $event->user->username,
                        'userId'   => $event->user->id,
                        'reason'   => (property_exists($event, 'switchReason') &&
                            $event->switchReason !== ''
                            ? $event->switchReason
                            : $this->translator->trans('fof-moderator-notes.api.no_reason_provided')),
                    ]
                )
            )
        );
    }
}
