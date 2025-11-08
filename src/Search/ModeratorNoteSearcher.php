<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Search;

use Flarum\Search\Database\AbstractSearcher;
use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Database\Eloquent\Builder;

class ModeratorNoteSearcher extends AbstractSearcher
{
    public function getQuery(mixed $actor): Builder
    {
        return ModeratorNote::query()->whereVisibleTo($actor);
    }
}
