<?php

namespace FoF\ModeratorNotes\Filter;

use Flarum\Filter\FilterInterface;
use Flarum\Filter\FilterState;
use Flarum\Filter\ValidateFilterTrait;
use FoF\ModeratorNotes\Repository\ModeratorNotesRepository;

class SubjectFilter implements FilterInterface
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
        return 'subject';
    }

    public function filter(FilterState $filterState, $filterValue, bool $negate)
    {
        $userIds = $this->asStringArray($filterValue);

        $ids = $this->notes->query()->whereIn('user_id', $userIds)->pluck('id');

        $filterState->getQuery()->whereIn('users_notes.id', $ids, 'and', $negate);
    }
}
