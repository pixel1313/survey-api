<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Tests\Functional\ApiTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testPostToCreateUser(): void
    {
        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'some_email@email.com',
                    'username' => 'some_name',
                    'password' => 'password',
                ],
            ])
            ->assertStatus(201)
            ->post('/login', [
                'json' => [
                    'email' => 'some_email@email.com',
                    'password' => 'password',
                ],
            ])
            ->assertSuccessful()
        ;
    }

    public function testPatchToUpdateUser(): void
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'username' => 'changed',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ])
            ->assertStatus(200)
        ;
    }
}
