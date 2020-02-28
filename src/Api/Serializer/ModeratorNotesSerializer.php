<?php

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
   * @return array
   */
  protected function getDefaultAttributes($moderatorNote)
  {
    return [
      'id' => $moderatorNote->id,
      'userId' => $moderatorNote->user_id,
      'addedByUser' => $moderatorNote->addedByUser,
      'note' => $moderatorNote->note,
      'createdAt' => $moderatorNote->created_at
    ];
  }

  protected function addedByUser($moderatorNote)
  {
    return $this->hasOne($moderatorNote, BasicUserSerializer::class);
  }
}
