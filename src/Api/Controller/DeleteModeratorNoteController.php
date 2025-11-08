<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Api\Controller;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use FoF\ModeratorNotes\Command\DeleteModeratorNote;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
class DeleteModeratorNoteController extends AbstractDeleteController
{
    public function __construct(protected Dispatcher $bus)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request): void
    {
        $actor = RequestUtil::getActor($request);

        $this->bus->dispatch(
            new DeleteModeratorNote(Arr::get($request->getQueryParams(), 'id'), $actor)
        );
    }
}
