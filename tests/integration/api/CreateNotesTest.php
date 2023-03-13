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
use Flarum\Formatter\Formatter;
use Flarum\Group\Group;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;

class CreateNotesTest extends TestCase
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

    // workaround to `flarum/testing` not clearing the Formatter cache
    // see https://github.com/flarum/framework/issues/3663
    protected function tearDown(): void
    {
        parent::tearDown();

        resolve(Formatter::class)->flush();
    }

    /**
     * @test
     */
    public function user_with_permission_can_create_note()
    {
        $response = $this->send(
            $this->request('POST', '/api/moderatorNote', [
                'authenticatedAs' => 3,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'userId' => 5,
                            'note'   => 'User posted against the guidelines',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(201, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertEquals(5, $response['data']['attributes']['userId']);
        $this->assertEquals('User posted against the guidelines', $response['data']['attributes']['note']);
        $this->assertArrayHasKey('createdAt', $response['data']['attributes']);
    }

    /**
     * @test
     */
    public function user_without_permission_cannot_create_note()
    {
        $response = $this->send(
            $this->request('POST', '/api/moderatorNote', [
                'authenticatedAs' => 4,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'userId' => 5,
                            'note'   => 'User posted against the guidelines',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_permission_can_create_note_with_markdown_enabled()
    {
        $this->extension('flarum-markdown');

        $response = $this->send(
            $this->request('POST', '/api/moderatorNote', [
                'authenticatedAs' => 3,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'userId' => 5,
                            'note'   => 'Some _input_ text',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(201, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertEquals(5, $response['data']['attributes']['userId']);
        $this->assertEquals('<p>Some <em>input</em> text</p>', $response['data']['attributes']['note']);
        $this->assertArrayHasKey('createdAt', $response['data']['attributes']);
    }
}
