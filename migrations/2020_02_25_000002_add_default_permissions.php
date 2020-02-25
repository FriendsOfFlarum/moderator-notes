<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'user.canViewModeratorNotes' => Group::MODERATOR_ID,
    'user.canAddModeratorNotes' => Group::MODERATOR_ID,
]);
