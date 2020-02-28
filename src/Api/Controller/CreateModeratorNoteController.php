<?php

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
