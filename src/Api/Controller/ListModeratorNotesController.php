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

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Query\QueryCriteria;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\UserRepository;
use FoF\ModeratorNotes\Api\Serializer\ModeratorNotesSerializer;
use FoF\ModeratorNotes\Filter\ModeratorNoteFilterer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListModeratorNotesController extends AbstractListController
{
    public $serializer = ModeratorNotesSerializer::class;

    public $include = ['addedByUser'];

    public $sortFields = ['createdAt'];

    /**
     * @var ModeratorNoteFilterer
     */
    protected $filterer;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(ModeratorNoteFilterer $filterer, UserRepository $users, UrlGenerator $url)
    {
        $this->filterer = $filterer;
        $this->users = $users;
        $this->url = $url;
    }

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
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('user.viewModeratorNotes');

        $filters = $this->extractFilter($request);
        $sort = $this->extractSort($request);
        $sortIsDefault = $this->sortIsDefault($request);

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $include = $this->extractInclude($request);

        $results = $this->filterer->filter(new QueryCriteria($actor, $filters, $sort, $sortIsDefault), $limit, $offset);

        $document->addPaginationLinks(
            $this->url->to('api')->route('moderator_notes.index'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $results->areMoreResults() ? null : 0
        );

        $results = $results->getResults();

        $this->loadRelations($results, $include, $request);

        return $results;
    }
}
