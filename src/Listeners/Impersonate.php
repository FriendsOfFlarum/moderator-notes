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

use Carbon\Carbon;
use FoF\Impersonate\Events\Impersonated;
use FoF\ModeratorNotes\Events\ModeratorNoteCreated;
use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class Impersonate
{
    public function __construct(protected TranslatorInterface $translator, protected Dispatcher $events)
    {
    }

    public function handle(Impersonated $event): void
    {
        $formatter = ModeratorNote::getFormatter();

        // Leave moderator note on impersonate subject
        $subjectNote = new ModeratorNote();
        $subjectNote->user_id = $event->user->id;
        $subjectNote->note = $formatter->parse(
            $this->translator->trans(
                'fof-moderator-notes.api.auto_note',
                [
                    'reason' => (property_exists($event, 'switchReason') &&
                        $event->switchReason !== ''
                        ? $event->switchReason
                        : $this->translator->trans('fof-moderator-notes.api.no_reason_provided')),
                ]
            )
        );
        $subjectNote->added_by_user_id = $event->actor->id;
        $subjectNote->created_at = Carbon::now();
        $subjectNote->save();

        $this->events->dispatch(new ModeratorNoteCreated($event->actor, $subjectNote));

        // Leave moderator note on impersonate actor
        $actorNote = new ModeratorNote();
        $actorNote->user_id = $event->actor->id;
        $actorNote->note = $formatter->parse(
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
        );
        $actorNote->added_by_user_id = $event->actor->id;
        $actorNote->created_at = Carbon::now();
        $actorNote->save();

        $this->events->dispatch(new ModeratorNoteCreated($event->actor, $actorNote));
    }
}
