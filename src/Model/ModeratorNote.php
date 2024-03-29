<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Model;

use Flarum\Database\AbstractModel;
use Flarum\Formatter\Formatter;
use Flarum\User\User;

/**
 * @property int       $user_id
 * @property string    $note
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property int       $added_by_user_id
 * @property User      $addedByUser
 */
class ModeratorNote extends AbstractModel
{
    protected $table = 'users_notes';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The text formatter instance.
     *
     * @var \Flarum\Formatter\Formatter
     */
    protected static $formatter;

    public function addedByUser()
    {
        return $this->hasOne(User::class, 'id', 'added_by_user_id');
    }

    /**
     * Get the text formatter instance.
     *
     * @return \Flarum\Formatter\Formatter
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set the text formatter instance.
     *
     * @param \Flarum\Formatter\Formatter $formatter
     */
    public static function setFormatter(Formatter $formatter)
    {
        static::$formatter = $formatter;
    }
}
