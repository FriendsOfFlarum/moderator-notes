<?php

namespace FoF\ModeratorNotes\Repository;

use FoF\ModeratorNotes\Model\ModeratorNote;

class ModeratorNotesRepository
{
/**
     * Get a new query builder for the users_notes table.
     *
     * @return Builder<ModeratorNote>
     */
    public function query()
    {
        return ModeratorNote::query();
    }
}
