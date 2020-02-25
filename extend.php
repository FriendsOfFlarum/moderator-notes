<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes;

use Flarum\Extend;
use FoF\ModeratorNotes\Api\Controller\UserModeratorNoteCreateController;
use FoF\ModeratorNotes\Api\Controller\UserModeratorNotesController;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),
    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Routes('api'))
        ->get('/notes/{id}', 'moderator_notes.index', UserModeratorNotesController::class)
        ->post('/notes', 'moderator-notes.create', UserModeratorNoteCreateController::class),

   function (Dispatcher $events) {
        $events->subscribe(Listeners\Permissions::class);
   }
];
