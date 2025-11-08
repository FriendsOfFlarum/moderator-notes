<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Api\Resource;

use Carbon\Carbon;
use Flarum\Api\Context;
use Flarum\Api\Endpoint;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Api\Sort\SortColumn;
use FoF\ModeratorNotes\Events\ModeratorNoteCreated;
use FoF\ModeratorNotes\Events\ModeratorNoteDeleted;
use FoF\ModeratorNotes\Model\ModeratorNote;
use Illuminate\Database\Eloquent\Builder;
use Tobyz\JsonApiServer\Context as ServerContext;

/**
 * @extends Resource\AbstractDatabaseResource<ModeratorNote>
 */
class ModeratorNoteResource extends Resource\AbstractDatabaseResource
{
    public function type(): string
    {
        return 'moderatorNote';
    }

    public function model(): string
    {
        return ModeratorNote::class;
    }

    public function scope(Builder $query, ServerContext $context): void
    {
        $query->whereVisibleTo($context->getActor());
    }

    public function endpoints(): array
    {
        return [
            Endpoint\Create::make()
                ->authenticated()
                ->visible(fn (Context $context) => $context->getActor()->hasPermission('user.createModeratorNotes'))
                ->defaultInclude(['addedByUser']),
            Endpoint\Delete::make()
                ->authenticated()
                ->can('delete'),
            Endpoint\Show::make()
                ->defaultInclude(['addedByUser']),
            Endpoint\Index::make()
                ->visible(fn (Context $context) => $context->getActor()->hasPermission('user.viewModeratorNotes'))
                ->defaultInclude(['addedByUser'])
                ->defaultSort('-createdAt')
                ->paginate(),
        ];
    }

    public function fields(): array
    {
        return [
            Schema\Integer::make('userId')
                ->writableOnCreate()
                ->requiredOnCreate(),

            Schema\Str::make('note')
                ->requiredOnCreate()
                ->writableOnCreate()
                ->minLength(1)
                ->set(function (ModeratorNote $note, string $value, Context $context) {
                    $formatter = ModeratorNote::getFormatter();
                    $note->note = $formatter->parse($value);
                })
                ->get(function (ModeratorNote $note, Context $context) {
                    $formatter = ModeratorNote::getFormatter();
                    return $formatter->render($note->note);
                }),

            Schema\DateTime::make('createdAt')
                ->get(fn (ModeratorNote $note) => $note->created_at),

            Schema\Relationship\ToOne::make('addedByUser')
                ->includable()
                ->type('users'),
        ];
    }

    public function creating(object $model, ServerContext $context): ?object
    {
        /** @var ModeratorNote $model */
        $model->added_by_user_id = $context->getActor()->id;
        $model->created_at = Carbon::now();

        return $model;
    }

    public function created(object $model, ServerContext $context): ?object
    {
        /** @var ModeratorNote $model */
        $this->events->dispatch(
            new ModeratorNoteCreated($context->getActor(), $model)
        );

        return $model;
    }

    public function deleting(object $model, ServerContext $context): void
    {
        /** @var ModeratorNote $model */
        $this->events->dispatch(
            new ModeratorNoteDeleted($context->getActor(), $model)
        );
    }

    public function sorts(): array
    {
        return [
            SortColumn::make('createdAt')
                ->ascendingAlias('oldest')
                ->descendingAlias('newest'),
        ];
    }
}
