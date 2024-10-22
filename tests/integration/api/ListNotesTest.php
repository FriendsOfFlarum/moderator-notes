<?php

/*
 * This file is part of fof/moderator-notes.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ModeratorNotes\Tests\integration;

use Carbon\Carbon;
use Flarum\Group\Group;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;

class ListNotesTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-moderator-notes');

        $this->prepareDatabase([
            'users' => [
                ['id' => 3, 'username' => 'a_moderator', 'email' => 'a_mod@machine.local', 'is_email_confirmed' => 1],
                ['id' => 4, 'username' => 'toby', 'email' => 'toby@machine.local', 'is_email_confirmed' => 1],
                ['id' => 5, 'username' => 'bad_user', 'email' => 'bad_user@machine.local', 'is_email_confirmed' => 1],
            ],
            'group_user' => [
                ['user_id' => 3, 'group_id' => Group::MODERATOR_ID],
            ],
            'users_notes' => [
                ['id' => 1, 'user_id' => 5, 'note' => '<t><p>bad_user has been naughty</p></t>', 'added_by_user_id' => 3, 'created_at' => Carbon::now()],
                ['id' => 2, 'user_id' => 4, 'note' => '<t><p>a moderator note</p></t>', 'added_by_user_id' => 3, 'created_at' => Carbon::now()],
                ['id' => 3, 'user_id' => 4, 'note' => '<t><p>another moderator note</p></t>', 'added_by_user_id' => 99, 'created_at' => Carbon::now()],
            ],
        ]);
    }

    /**
     * @test
     */
    public function guest_user_cannot_list_notes()
    {
        $response = $this->send(
            $this->request('GET', '/api/moderatorNote')
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function normal_user_cannot_list_notes()
    {
        $this->database();
        $this->assertFalse(User::find(4)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote', [
                'authenticatedAs' => 4,
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_permission_can_list_notes_for_user()
    {
        $this->database();
        $this->assertTrue(User::find(3)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote', [
                'authenticatedAs' => 3,
            ])->withQueryParams([
                'filter' => ['subject' => '5'],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertIsArray($response['data']);

        $this->assertCount(1, $response['data']);

        $this->assertStringContainsString('bad_user has been naughty', $response['data'][0]['attributes']['note']);
        $this->assertEquals(1, $response['data'][0]['id']);
    }

    /**
     * @test
     */
    public function user_with_permission_can_list_notes_by_author()
    {
        $this->database();
        $this->assertTrue(User::find(3)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote', [
                'authenticatedAs' => 3,
            ])->withQueryParams([
                'filter' => ['author' => '99'],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertIsArray($response['data']);

        $this->assertCount(1, $response['data']);

        $this->assertStringContainsString('another moderator note', $response['data'][0]['attributes']['note']);
        $this->assertEquals(3, $response['data'][0]['id']);
    }

    /**
     * @test
     */
    public function user_with_permission_can_list_notes_by_author_and_subject()
    {
        $this->database();
        $this->assertTrue(User::find(3)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote', [
                'authenticatedAs' => 3,
            ])->withQueryParams([
                'filter' => ['author' => '99', 'subject' => '4'],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertIsArray($response['data']);

        $this->assertCount(1, $response['data']);

        $this->assertStringContainsString('another moderator note', $response['data'][0]['attributes']['note']);
        $this->assertEquals(3, $response['data'][0]['id']);
    }

    /**
     * @test
     */
    public function user_with_permission_can_list_notes_by_subject_when_multiple_records_are_present()
    {
        $this->database();
        $this->assertTrue(User::find(3)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote', [
                'authenticatedAs' => 3,
            ])->withQueryParams([
                'filter' => ['subject' => '4'],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertIsArray($response['data']);

        $this->assertCount(2, $response['data']);

        $this->assertStringContainsString('a moderator note', $response['data'][0]['attributes']['note']);
        $this->assertEquals(2, $response['data'][0]['id']);
        $this->assertStringContainsString('another moderator note', $response['data'][1]['attributes']['note']);
        $this->assertEquals(3, $response['data'][1]['id']);
    }

    /**
     * @test
     */
    public function user_with_permission_can_list_all_notes()
    {
        $this->database();
        $this->assertTrue(User::find(3)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote', [
                'authenticatedAs' => 3,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertIsArray($response['data']);

        $this->assertCount(3, $response['data']);
    }
}
