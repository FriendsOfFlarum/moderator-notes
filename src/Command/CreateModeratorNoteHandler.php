<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Command;

use Flarum\Foundation\ValidationException;
use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Support\Carbon;

class CreateModeratorNoteHandler
{
    /**
     * @param CreateNote $command
     *
     * @return ModeratorNote
     */
    public function handle(CreateModeratorNote $command)
    {
        $actor = $command->actor;
        $user_id = $command->user_id;
        $notecontent = $command->note;

        $note = new ModeratorNote();
        $note->user_id = $user_id;
        $note->note = $notecontent;
        $note->added_by_user_id = $actor->id;
        $note->created_at = Carbon::now();

        if ($note->note === '') {
            throw new ValidationException(['message' => $this->translator->trans('fof-moderator-notes.forum.no_content_given')]);
        }

        $note->save();

        return $note;
    }
}
