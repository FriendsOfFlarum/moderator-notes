<?php

namespace FoF\ModeratorNotes\Api\Controller;

use Carbon\Carbon;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Foundation\ValidationException;
use Flarum\User\AssertPermissionTrait;
use Flarum\User\Exception\PermissionDeniedException;
use FoF\ModeratorNotes\Model\ModeratorNote;
use FoF\ModeratorNotes\Api\Serializer\ModeratorNotesSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tobscure\JsonApi\Document;

class UserModeratorNoteCreateController extends AbstractListController
{
    use AssertPermissionTrait;

    public $serializer = ModeratorNotesSerializer::class;

    protected $translator;

    public function __construct(TranslatorInterface $trans)
    {
        $this->translator = $trans;
    }

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
        $actor = $request->getAttribute('actor');
        $this->assertCan($actor, 'user.createModeratorNotes');
        $requestBody = $request->getParsedBody();

        $requestData = $requestBody['data']['attributes'];

        $note = new ModeratorNote();
        $note->user_id = $requestData['userId'];
        $note->note = $requestData['note'];
        $note->added_by_user_id = $actor->id;
        $note->created_at = Carbon::now();

        if ($note->note === ''){
            throw new ValidationException(['message' => $this->translator->trans('fof-moderator-notes.forum.no_content_given')]);
        }

        $note->save();

        return $note;
    }
}
