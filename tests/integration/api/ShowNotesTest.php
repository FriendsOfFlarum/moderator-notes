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
use FoF\ModeratorNotes\Model\ModeratorNote;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ShowNotesTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-moderator-notes');

        $this->prepareDatabase([
            User::class => [
                ['id' => 3, 'username' => 'a_moderator', 'email' => 'a_mod@machine.local', 'is_email_confirmed' => 1],
                ['id' => 4, 'username' => 'toby', 'email' => 'toby@machine.local', 'is_email_confirmed' => 1],
                ['id' => 5, 'username' => 'bad_user', 'email' => 'bad_user@machine.local', 'is_email_confirmed' => 1],
            ],
            'group_user' => [
                ['user_id' => 3, 'group_id' => Group::MODERATOR_ID],
            ],
            ModeratorNote::class => [
                ['id' => 1, 'user_id' => 5, 'note' => '<t><p>bad_user has been naughty</p></t>', 'added_by_user_id' => 3, 'created_at' => Carbon::now()],
                ['id' => 2, 'user_id' => 4, 'note' => '<t><p>a moderator note about toby</p></t>', 'added_by_user_id' => 3, 'created_at' => Carbon::now()],
            ],
        ]);
    }

    public static function unauthorizedUserProvider(): array
    {
        return [
            'guest' => [null],
            'normal user' => [4],
        ];
    }

    #[Test]
    #[DataProvider('unauthorizedUserProvider')]
    public function unauthorized_users_cannot_show_note(?int $authenticatedAs)
    {
        $response = $this->send(
            $this->request('GET', '/api/moderatorNote/1', [
                'authenticatedAs' => $authenticatedAs,
            ])
        );

        $this->assertEquals(404, $response->getStatusCode());
    }

    public static function authorizedUserProvider(): array
    {
        return [
            'admin' => [1],
            'moderator' => [3],
        ];
    }

    #[Test]
    #[DataProvider('authorizedUserProvider')]
    public function authorized_users_can_show_note(int $authenticatedAs)
    {
        $response = $this->send(
            $this->request('GET', '/api/moderatorNote/1', [
                'authenticatedAs' => $authenticatedAs,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertEquals('1', $response['data']['id']);
        $this->assertStringContainsString('bad_user has been naughty', $response['data']['attributes']['note']);
        $this->assertEquals(5, $response['data']['attributes']['userId']);
        $this->assertArrayHasKey('createdAt', $response['data']['attributes']);

        // Check that addedByUser is included by default
        $this->assertArrayHasKey('included', $response);
        $this->assertCount(1, $response['included']);
        $this->assertEquals('users', $response['included'][0]['type']);
        $this->assertEquals('3', $response['included'][0]['id']);
    }

    #[Test]
    public function user_with_permission_cannot_show_nonexistent_note()
    {
        $response = $this->send(
            $this->request('GET', '/api/moderatorNote/999', [
                'authenticatedAs' => 3,
            ])
        );

        $this->assertEquals(404, $response->getStatusCode());
    }
}
