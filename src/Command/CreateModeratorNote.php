<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Command;

use Flarum\User\User;

class CreateModeratorNote
{
    public function __construct(public User $actor, public int $user_id, public string $note)
    {
    }
}
