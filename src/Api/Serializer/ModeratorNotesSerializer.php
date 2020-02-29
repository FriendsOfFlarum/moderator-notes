<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

class ModeratorNotesSerializer extends AbstractSerializer
{
    protected $type = 'moderatorNotes';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param object|array $model
     *
     * @return array
     */
    protected function getDefaultAttributes($moderatorNote)
    {
        return [
            'id'        => $moderatorNote->id,
            'userId'    => $moderatorNote->user_id,
            'note'      => $moderatorNote->note,
            'createdAt' => $this->formatDate($moderatorNote->created_at),
        ];
    }

    protected function addedByUser($moderatorNote)
    {
        return $this->hasOne($moderatorNote, BasicUserSerializer::class);
    }
}
