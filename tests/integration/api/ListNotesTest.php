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
                ['id' => 6, 'user_id' => 5, 'note' => '<t><p>bad_user has been naughty</p></t>', 'added_by_user_id' => 3, 'created_at' => Carbon::now()],
            ],
        ]);
    }

    /**
     * @test
     */
    public function guest_user_cannot_list_notes()
    {
        $response = $this->send(
            $this->request('GET', '/api/moderatorNote/5')
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
            $this->request('GET', '/api/moderatorNote/5', [
                'authenticatedAs' => 4,
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_permission_can_list_notes()
    {
        $this->database();
        $this->assertTrue(User::find(3)->can('user.viewModeratorNotes'));

        $response = $this->send(
            $this->request('GET', '/api/moderatorNote/5', [
                'authenticatedAs' => 3,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertIsArray($response['data']);
    }
}
