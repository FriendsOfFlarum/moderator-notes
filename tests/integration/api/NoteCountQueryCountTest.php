<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Tests\integration\api;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use PHPUnit\Framework\Attributes\Test;

/**
 * The moderatorNoteCount user attribute issued one COUNT query per
 * serialized user. On a discussion list viewed by a moderator that meant one
 * query per distinct user on the page. These tests pin the batched
 * behaviour: the users_notes table must be queried a constant number of
 * times regardless of how many users appear in the document.
 */
class NoteCountQueryCountTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-moderator-notes');

        $users = [$this->normalUser()];
        $discussions = [];

        // Five discussions, each started and last posted by a different user.
        for ($i = 0; $i < 5; $i++) {
            $userId = 3 + $i;

            $users[] = [
                'id'                 => $userId,
                'username'           => 'poster'.$userId,
                'email'              => 'poster'.$userId.'@machine.local',
                'is_email_confirmed' => 1,
                'password'           => 'foobar',
            ];

            $discussions[] = [
                'id'                  => 1 + $i,
                'title'               => 'Discussion '.$i,
                'created_at'          => Carbon::createFromDate(2024, 1, 1 + $i)->toDateTimeString(),
                'last_posted_at'      => Carbon::createFromDate(2024, 1, 1 + $i)->toDateTimeString(),
                'user_id'             => $userId,
                'last_posted_user_id' => $userId,
                'comment_count'       => 1,
            ];
        }

        $this->prepareDatabase([
            User::class => $users,
            Discussion::class => $discussions,
            'users_notes' => [
                [
                    'id'                => 1,
                    'user_id'           => 3,
                    'note'              => '<t><p>First note</p></t>',
                    'added_by_user_id'  => 1,
                    'created_at'        => Carbon::parse('2024-01-01')->toDateTimeString(),
                ],
                [
                    'id'                => 2,
                    'user_id'           => 3,
                    'note'              => '<t><p>Second note</p></t>',
                    'added_by_user_id'  => 1,
                    'created_at'        => Carbon::parse('2024-01-02')->toDateTimeString(),
                ],
            ],
        ]);
    }

    #[Test]
    public function note_counts_are_loaded_with_a_constant_number_of_queries()
    {
        $this->app();

        $db = $this->database();
        $db->flushQueryLog();
        $db->enableQueryLog();

        $response = $this->send(
            $this->request('GET', '/api/discussions', ['authenticatedAs' => 1])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $noteQueries = 0;

        foreach ($db->getQueryLog() as $query) {
            if (stripos($query['query'], 'users_notes') !== false) {
                $noteQueries++;
            }
        }

        $body = json_decode($response->getBody()->getContents(), true);

        // Sanity: the attribute is serialized and correct — user 3 has two
        // notes, user 4 none.
        $countsById = [];

        foreach ($body['included'] ?? [] as $resource) {
            if ($resource['type'] === 'users') {
                $countsById[$resource['id']] = $resource['attributes']['moderatorNoteCount'] ?? null;
            }
        }

        $this->assertSame(2, $countsById['3'] ?? null);
        $this->assertSame(0, $countsById['4'] ?? null);

        $this->assertSame(
            1,
            $noteQueries,
            "Note counts for all serialized users must be loaded in one batched query, not one per user (got $noteQueries)."
        );
    }
}
