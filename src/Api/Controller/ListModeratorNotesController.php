<?php

namespace FoF\ModeratorNotes\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\User\AssertPermissionTrait;
use Flarum\User\Exception\PermissionDeniedException;
use FoF\ModeratorNotes\Model\ModeratorNote;
use FoF\ModeratorNotes\Api\Serializer\ModeratorNotesSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListModeratorNotesController extends AbstractListController
{
    use AssertPermissionTrait;

    public $serializer = ModeratorNotesSerializer::class;

    public $include = ['addedByUser'];

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return mixed
     * @throws PermissionDeniedException
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = array_get($request->getQueryParams(), 'id');

        $actor = $request->getAttribute('actor');

        $this->assertCan($actor, 'user.viewModeratorNotes');

        return ModeratorNote::where('user_id', $id)->orderBy('created_at', 'desc')->get();
    }
}
