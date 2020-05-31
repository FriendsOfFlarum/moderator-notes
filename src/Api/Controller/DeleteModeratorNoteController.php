<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Api\Controller;

use Flarum\Api\Controller\AbstractDeleteController;
use FoF\ModeratorNotes\Command\DeleteModeratorNote;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeleteModeratorNoteController extends AbstractDeleteController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request)
    {
        $actor = $request->getAttribute('actor');

        $this->bus->dispatch(
            new DeleteModeratorNote(Arr::get($request->getQueryParams(), 'id'), $actor)
        );
    }
}
