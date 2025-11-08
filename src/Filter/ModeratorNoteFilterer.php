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

use Flarum\Filter\AbstractFilterer;
use Flarum\User\User;
use FoF\ModeratorNotes\Repository\ModeratorNotesRepository;
use Illuminate\Database\Eloquent\Builder;

class ModeratorNoteFilterer extends AbstractFilterer
{
    /**
     * @param array                    $filterMutators
     */
    public function __construct(protected ModeratorNotesRepository $notes, array $filters, array $filterMutators)
    {
        parent::__construct($filters, $filterMutators);
    }

    protected function getQuery(User $actor): Builder
    {
        return $this->notes->query()->select('users_notes.*'); //->whereVisibleTo($actor);
    }
}
