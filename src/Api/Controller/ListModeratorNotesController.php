<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) 2020 - 2021 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\User\Exception\PermissionDeniedException;
use FoF\ModeratorNotes\Api\Serializer\ModeratorNotesSerializer;
use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListModeratorNotesController extends AbstractListController
{
    public $serializer = ModeratorNotesSerializer::class;

    public $include = ['addedByUser'];

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @throws PermissionDeniedException
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = Arr::get($request->getQueryParams(), 'id');

        /**
         * @var \Flarum\User\User $actor
         */
        $actor = $request->getAttribute('actor');

        $actor->assertCan('user.viewModeratorNotes');

        return ModeratorNote::where('user_id', $id)->orderBy('created_at', 'desc')->get();
    }
}
