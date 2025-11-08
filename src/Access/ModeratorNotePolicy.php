<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use FoF\ModeratorNotes\Model\ModeratorNote;

class ModeratorNotePolicy extends AbstractPolicy
{
    public function delete(User $actor, ModeratorNote $note): string
    {
        if ($actor->hasPermission('user.deleteModeratorNotes')) {
            return $this->allow();
        }

        return $this->deny();
    }
}
