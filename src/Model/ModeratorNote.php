<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

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
