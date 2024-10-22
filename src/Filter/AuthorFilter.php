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

use Flarum\Filter\FilterInterface;
use Flarum\Filter\FilterState;
use Flarum\Filter\ValidateFilterTrait;
use FoF\ModeratorNotes\Repository\ModeratorNotesRepository;

class AuthorFilter implements FilterInterface
{
    use ValidateFilterTrait;

    /**
     * @var ModeratorNotesRepository
     */
    protected $notes;

    public function __construct(ModeratorNotesRepository $notes)
    {
        $this->notes = $notes;
    }

    public function getFilterKey(): string
    {
        return 'author';
    }

    public function filter(FilterState $filterState, $filterValue, bool $negate)
    {
        $userIds = $this->asStringArray($filterValue);

        $ids = $this->notes->query()->whereIn('added_by_user_id', $userIds)->pluck('id');

        $filterState->getQuery()->whereIn('users_notes.id', $ids, 'and', $negate);
    }
}
