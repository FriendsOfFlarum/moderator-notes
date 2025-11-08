<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Filter;

use Flarum\Search\Filter\FilterInterface;
use Flarum\Search\SearchState;
use Flarum\Search\Filter\ValidateFilterTrait;
use FoF\ModeratorNotes\Repository\ModeratorNotesRepository;

class SubjectFilter implements FilterInterface
{
    use ValidateFilterTrait;

    public function __construct(protected ModeratorNotesRepository $notes)
    {
    }

    public function getFilterKey(): string
    {
        return 'subject';
    }

    public function filter(SearchState $state, array|string $value, bool $negate): void
    {
        $userIds = $this->asStringArray($value);

        $ids = $this->notes->query()->whereIn('user_id', $userIds)->pluck('id');

        $state->getQuery()->whereIn('users_notes.id', $ids, 'and', $negate);
    }
}
