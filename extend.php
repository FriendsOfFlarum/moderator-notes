<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes;

use Flarum\Api\Resource;
use Flarum\Extend;
use Flarum\Search\Database\DatabaseSearchDriver;
use FoF\Impersonate\Events\Impersonated;
use FoF\ModeratorNotes\Model\ModeratorNote;
use FoF\ModeratorNotes\Provider\ModeratorNotesProvider;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->jsDirectory(__DIR__.'/js/dist/forum')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Event())
        ->listen(Impersonated::class, Listeners\Impersonate::class),

    (new Extend\ServiceProvider())
        ->register(ModeratorNotesProvider::class),

    new Extend\ApiResource(Api\Resource\ModeratorNoteResource::class),

    (new Extend\ApiResource(Resource\UserResource::class))
        ->fields(Api\UserResourceFields::class),

    (new Extend\Policy())
        ->modelPolicy(ModeratorNote::class, Access\ModeratorNotePolicy::class),

    (new Extend\SearchDriver(DatabaseSearchDriver::class))
        ->addSearcher(ModeratorNote::class, Search\ModeratorNoteSearcher::class)
        ->addFilter(Search\ModeratorNoteSearcher::class, Search\Filter\SubjectFilter::class)
        ->addFilter(Search\ModeratorNoteSearcher::class, Search\Filter\AuthorFilter::class),
];
