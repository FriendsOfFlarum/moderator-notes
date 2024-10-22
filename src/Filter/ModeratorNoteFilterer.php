<?php

namespace FoF\ModeratorNotes\Filter;

use Flarum\Filter\AbstractFilterer;
use Flarum\User\User;
use FoF\ModeratorNotes\Repository\ModeratorNotesRepository;
use Illuminate\Database\Eloquent\Builder;

class ModeratorNoteFilterer extends AbstractFilterer
{
    /**
     * @var ModeratorNotesRepository
     */
    protected $notes;

    /**
     * @param PostRepository $posts
     * @param array $filters
     * @param array $filterMutators
     */
    public function __construct(ModeratorNotesRepository $notes, array $filters, array $filterMutators)
    {
        parent::__construct($filters, $filterMutators);

        $this->notes = $notes;
    }

    protected function getQuery(User $actor): Builder
    {
        return $this->notes->query()->select('users_notes.*');//->whereVisibleTo($actor);
    }
}
