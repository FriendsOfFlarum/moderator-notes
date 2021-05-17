<?php

namespace FoF\ModeratorNotes\Provider;

use Flarum\Formatter\Formatter;
use Flarum\Foundation\AbstractServiceProvider;
use FoF\ModeratorNotes\Model\ModeratorNote;

class ModeratorNotesProvider extends AbstractServiceProvider
{
    public function register()
    {
        ModeratorNote::setFormatter($this->container->make(Formatter::class));
    }
}
