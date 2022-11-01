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

class UserAttributesTest extends TestCase
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
                ['id' => 6, 'user_id' => 5, 'note' => '<t><p>bad_user has been naughty</p></t>', 'added_by_user_id' => 3, 'created_at' => Carbon::now()],
            ],
        ]);
    }

    /**
     * @test
     */
    public function guest_user_does_not_have_moderator_notes_permission_attrs()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/4')
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertArrayNotHasKey('canViewModeratorNotes', $response['data']['attributes']);
        $this->assertArrayNotHasKey('canCreateModeratorNotes', $response['data']['attributes']);
        $this->assertArrayNotHasKey('canDeleteModeratorNotes', $response['data']['attributes']);
        $this->assertArrayNotHasKey('moderatorNoteCount', $response['data']['attributes']);
    }

    /**
     * @test
     */
    public function normal_user_does_not_have_moderator_notes_permission_attrs()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/4', [
                'authenticatedAs' => 4,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertArrayNotHasKey('canViewModeratorNotes', $response['data']['attributes']);
        $this->assertArrayNotHasKey('canCreateModeratorNotes', $response['data']['attributes']);
        $this->assertArrayNotHasKey('canDeleteModeratorNotes', $response['data']['attributes']);
        $this->assertArrayNotHasKey('moderatorNoteCount', $response['data']['attributes']);
    }

    /**
     * @test
     */
    public function moderator_user_has_view_and_create_moderator_notes_permission_attrs()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/3', [
                'authenticatedAs' => 3,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('canViewModeratorNotes', $response['data']['attributes']);
        $this->assertEquals(true, $response['data']['attributes']['canViewModeratorNotes']);
        $this->assertArrayHasKey('canCreateModeratorNotes', $response['data']['attributes']);
        $this->assertEquals(true, $response['data']['attributes']['canCreateModeratorNotes']);
        $this->assertArrayNotHasKey('canDeleteModeratorNotes', $response['data']['attributes']);
        $this->assertArrayHasKey('moderatorNoteCount', $response['data']['attributes']);
        $this->assertEquals(0, $response['data']['attributes']['moderatorNoteCount']);
    }

    /**
     * @test
     */
    public function moderator_user_has_view_and_create_and_delete_moderator_notes_permission_attrs()
    {
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => Group::MEMBER_ID, 'permission' => 'user.deleteModeratorNotes'],
            ],
        ]);

        $response = $this->send(
            $this->request('GET', '/api/users/3', [
                'authenticatedAs' => 3,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('canViewModeratorNotes', $response['data']['attributes']);
        $this->assertEquals(true, $response['data']['attributes']['canViewModeratorNotes']);
        $this->assertArrayHasKey('canCreateModeratorNotes', $response['data']['attributes']);
        $this->assertEquals(true, $response['data']['attributes']['canCreateModeratorNotes']);
        $this->assertArrayHasKey('canDeleteModeratorNotes', $response['data']['attributes']);
        $this->assertEquals(true, $response['data']['attributes']['canDeleteModeratorNotes']);
    }

    /**
     * @test
     */
    public function moderator_can_see_note_count_attr()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/5', [
                'authenticatedAs' => 3,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('moderatorNoteCount', $response['data']['attributes']);
        $this->assertEquals(1, $response['data']['attributes']['moderatorNoteCount']);
    }

    /**
     * @test
     */
    public function normal_user_cannot_see_note_count_attr()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/5', [
                'authenticatedAs' => 4,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertArrayNotHasKey('moderatorNoteCount', $response['data']['attributes']);
    }
}
