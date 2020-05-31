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

use Flarum\User\AssertPermissionTrait;
use FoF\ModeratorNotes\Model\ModeratorNote;

class DeleteModeratorNoteHandler
{
    use AssertPermissionTrait;

    /**
     * @param DeleteModeratorNote $command
     *
     * @throws \Flarum\User\Exception\PermissionDeniedException
     *
     * @return mixed
     */
    public function handle(DeleteModeratorNote $command)
    {
        $actor = $command->actor;
        $this->assertCan($actor, 'user.deleteModeratorNotes');

        $note = ModeratorNote::findOrFail($command->noteId);

        $note->delete();

        return $note;
    }
}
