<?php

namespace FoF\ModeratorNotes\Model;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class ModeratorNote extends AbstractModel
{
    protected $table = 'users_notes';

    public function addedByUser()
    {
        return $this->hasOne(User::class, 'id', 'added_by_user_id');
    }
}
