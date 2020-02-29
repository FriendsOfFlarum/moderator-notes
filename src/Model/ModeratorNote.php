<?php

namespace FoF\ModeratorNotes\Model;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

/**
 * @property string note
 * @property Date
 * @property User addedByUser
 */
class ModeratorNote extends AbstractModel
{
    protected $table = 'users_notes';

    protected $dates = ['created_at'];

    public function addedByUser()
    {
        return $this->hasOne(User::class, 'id', 'added_by_user_id');
    }
}
