<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'user.viewModeratorNotes' => Group::MODERATOR_ID,
    'user.createModeratorNotes' => Group::MODERATOR_ID,
]);
