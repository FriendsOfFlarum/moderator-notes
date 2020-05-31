<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $formatter = ModeratorNote::getFormatter();
        
        foreach (ModeratorNote::get() as $moderatorNote) {
            $moderatorNote->note = $formatter->parse($moderatorNote->note);
            $moderatorNote->save();
        }
    },

    'down' => function (Builder $schema) {
        // changes should be kept
    },
];
