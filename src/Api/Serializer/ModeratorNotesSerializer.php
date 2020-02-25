<?php

namespace FoF\ModeratorNotes\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class ModeratorNotesSerializer extends AbstractSerializer {

  protected $type = 'moderatorNotes';

  /**
   * Get the default set of serialized attributes for a model.
   *
   * @param object|array $model
   * @return array
   */
  protected function getDefaultAttributes($moderatorNotes)
  {
      $attributes = [
        'id' => $moderatorNotes->id,
        'userId' => $moderatorNotes->user_id,
        'note' => $moderatorNotes->note,
        'addedByUser' => [
          'username' => $moderatorNotes->addedByUser->username,
          'displayName' => $moderatorNotes->addedByUser->display_name,
          'avatarUrl' => $moderatorNotes->addedByUser->avatar_url,
        ],
        'createdAt' => $moderatorNotes->created_at,
        'updatedAt' => $moderatorNotes->updated_at
      ];

      return $attributes;
  }
}
