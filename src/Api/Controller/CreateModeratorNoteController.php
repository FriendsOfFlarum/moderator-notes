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

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\User\AssertPermissionTrait;
use FoF\ModeratorNotes\Api\Serializer\ModeratorNotesSerializer;
use FoF\ModeratorNotes\Command\CreateModeratorNote;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateModeratorNoteController extends AbstractCreateController
{
    use AssertPermissionTrait;

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
        $actor = $request->getAttribute('actor');
        $this->assertCan($actor, 'user.createModeratorNotes');

        $requestBody = $request->getParsedBody();
        $requestData = $requestBody['data']['attributes'];

        return $this->bus->dispatch(
            new CreateModeratorNote($actor, $requestData['userId'], $requestData['note'])
        );
    }
}
