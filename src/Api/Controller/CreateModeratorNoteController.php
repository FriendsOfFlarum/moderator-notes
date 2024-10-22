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

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use FoF\ModeratorNotes\Api\Serializer\ModeratorNotesSerializer;
use FoF\ModeratorNotes\Command\CreateModeratorNote;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateModeratorNoteController extends AbstractCreateController
{
    public $serializer = ModeratorNotesSerializer::class;

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
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('user.createModeratorNotes');

        $requestBody = $request->getParsedBody();
        $data = Arr::get($requestBody, 'data.attributes', []);

        $userId = Arr::get($data, 'userId');
        $noteData = Arr::get($data, 'note');

        return $this->bus->dispatch(
            new CreateModeratorNote($actor, $userId, $noteData)
        );
    }
}
