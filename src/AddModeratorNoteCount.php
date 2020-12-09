<?php

namespace FoF\ModeratorNotes;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use FoF\ModeratorNotes\Model\ModeratorNote;

class AddModeratorNoteCount
{
    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        if ($serializer->getActor()->can('viewModeratorNotes', $user)) {
            $attributes['moderatorNoteCount'] = ModeratorNote::where('user_id', $user->id)->count();
        }

        return $attributes;
    }
}