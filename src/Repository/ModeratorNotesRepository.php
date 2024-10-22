<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Repository;

use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Database\Eloquent\Builder;

class ModeratorNotesRepository
{
    /**
     * Get a new query builder for the users_notes table.
     *
     * @return Builder<ModeratorNote>
     */
    public function query()
    {
        return ModeratorNote::query();
    }
}
