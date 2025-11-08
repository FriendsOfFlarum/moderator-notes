<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Api;

use Flarum\Api\Context;
use Flarum\Api\Schema;
use Flarum\User\User;
use FoF\ModeratorNotes\Repository\ModeratorNotesRepository;

class UserResourceFields
{
    public function __construct(protected ModeratorNotesRepository $notes)
    {
    }

    public function __invoke(): array
    {
        return [
            Schema\Boolean::make('canViewModeratorNotes')
                ->visible(fn (User $user, Context $context) => $context->getActor()->can('viewModeratorNotes', $user))
                ->get(fn (User $user, Context $context) => $context->getActor()->can('viewModeratorNotes', $user)),

            Schema\Boolean::make('canCreateModeratorNotes')
                ->visible(fn (User $user, Context $context) => $context->getActor()->can('createModeratorNotes', $user))
                ->get(fn (User $user, Context $context) => $context->getActor()->can('createModeratorNotes', $user)),

            Schema\Boolean::make('canDeleteModeratorNotes')
                ->visible(fn (User $user, Context $context) => $context->getActor()->hasPermission('user.deleteModeratorNotes'))
                ->get(fn (User $user, Context $context) => $context->getActor()->hasPermission('user.deleteModeratorNotes')),

            Schema\Integer::make('moderatorNoteCount')
                ->visible(fn (User $user, Context $context) => $context->getActor()->can('viewModeratorNotes', $user))
                ->get(function (User $user, Context $context) {
                    return $this->notes->query()->where('user_id', $user->id)->count();
                }),
        ];
    }
}
