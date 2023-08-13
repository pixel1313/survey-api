<?php

namespace App\Tests\Functional;
use App\Entity\ApiToken;
use App\Entity\User;
use App\Factory\ApiTokenFactory;
use App\Factory\PublisherFactory;
use App\Factory\UserFactory;
use App\Tests\Component\PublisherComponent;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Summary of PublisherResourceTest
 * 
 * @todo add security exception tests.
 * @todo add authentication.
 * @todo check that users can't access collections of publishers.
 */
class PublisherResourceTest extends ApiTestCase
{
    use ResetDatabase;

    /**
     * Get a collection of publishers and verify that the right number come back and the format is correct.
     * @return void
     * 
     * @todo test pagination.
     * @todo test security.
     * @todo extract the admin tests to another Test class.
     * 
     * @group GET
     * @group PublisherCollection
     * @group Publisher
     * @group json
     */
    public function testGetCollectionOfPublishers(): void
    {
        
        $publisher = PublisherFactory::createOne();

        $badToken = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_SURVEY_VIEW],
            'ownedBy' => UserFactory::createOne(['publisher' => $publisher]),
        ]);

        $userToken = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_PUBLISHER_VIEW],
            'ownedBy' => UserFactory::createOne(['publisher' => $publisher]),
        ]);

        
        // fixtures for admins
        $adminToken = ApiTokenFactory::createOne([
            'scopes' => [User::ADMIN],
            'ownedBy' => UserFactory::createOne(['publisher' => $publisher]),
        ]);
        
        PublisherFactory::createMany(4);

        $this->browser()
            // test without permissions.
            ->use(function (PublisherComponent $publisherComponent) use ($badToken) {
                $publisherComponent->getPublisherCollectionWithToken($badToken->getToken());
            })
            //->assertStatus(403)
            // test that we only get one and it has the correct format.
            ->use(function (PublisherComponent $publisherComponent) use ($userToken) {
                $publisherComponent->getPublisherCollectionWithToken($userToken->getToken());
            })
            ->assertJson()
            ->assertJsonMatches('"hydra:totalItems"', 1)
            ->assertJsonMatches('length("hydra:member")', 1)
            ->assertJsonMatches('keys("hydra:member"[0])', [
                '@id',
                '@type',
                'id',
                'name',
            ])
            // test that we get all of the publishers if we're an admin
            ->use(function (PublisherComponent $publisherComponent) use ($adminToken) {
                $publisherComponent->getPublisherCollectionWithToken($adminToken->getToken());
            })
            ->assertJson()
            ->assertJsonMatches('"hydra:totalItems"', 5)
            ->assertJsonMatches('length("hydra:member")', 5)
            ->assertJsonMatches('keys("hydra:member"[0])', [
                '@id',
                '@type',
                'id',
                'name',
            ])
        ;
    }

    /**
     * Get a single publisher and verify the format.
     * @return void
     * 
     * @todo test security.
     * 
     * @group GET
     * @group Publisher
     */
    public function testGetOnePublisher(): void
    {
        $publisher = PublisherFactory::createOne();

        $this->browser()
            ->use(function (PublisherComponent $publisherComponent) use ($publisher) {
                $publisherComponent->getPublisherItem($publisher->getId());
            });
    }

    /**
     * Create a Publisher.
     * @return void
     * 
     * @group POST
     * @group Security
     * @group Publisher
     */
    public function testPostToCreatePublisher(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_PUBLISHER_CREATE],
        ]);

        $badToken = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_PUBLISHER_VIEW],
        ]);

        $this->browser()
            // test to make sure it fails without correct permissions
            // test to make sure it fails with validation errors when we don't have the correct payload.
            ->use(function (PublisherComponent $publisherComponent) use ($token) {
                $publisherComponent->createWithToken([], $token->getToken());
            })
            ->assertStatus(422)
            ->use(function (PublisherComponent $publisherComponent) use ($badToken) {
                $publisherComponent->createWithToken([
                    'name' => 'Bad Publisher',
                ], $badToken->getToken());
            })
            ->assertStatus(403)
            // test to make sure it creates when we do have the correct payload.
            ->use(function (PublisherComponent $publisherComponent) use ($token) {
                $publisherComponent->createWithToken([
                    'name' => 'New Publisher',
                ], $token->getToken());
            })
            ->assertStatus(201)
        ;
    }

    public function testPatchToUpdatePublisher(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_PUBLISHER_EDIT],
        ]);

        $badToken = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_PUBLISHER_VIEW],
        ]);

        $publisher = PublisherFactory::createOne([
            'name' => 'Old Name'
        ]);

        $this->browser()
            // test to make sure it fails with 404 if the publisher doesn't exist.
            ->use(function (PublisherComponent $publisherComponent) use ($publisher, $token) {
                $publisherComponent->updatePublisherWithToken($publisher->getId() + 1, [
                    'name' => 'New Name'
                ], $token->getToken());
            })
            ->assertStatus(404)
            // test to make sure it fails with 422 if there are validation errors.
            ->use(function (PublisherComponent $publisherComponent) use ($publisher, $token) {
                $publisherComponent->updatePublisherWithToken($publisher->getId(), [
                    'name' => '',
                ], $token->getToken());
            })
            ->assertStatus(422)
            // test to make sure it fails with 403 if we don't have permissions.
            ->use(function (PublisherComponent $publisherComponent) use ($publisher, $badToken) {
                $publisherComponent->updatePublisherWithToken($publisher->getId(), [
                    'name' => 'New Name',
                ], $badToken->getToken());
            })
            ->assertStatus(403)
            // test to make sure it updates correctly
            ->use(function (PublisherComponent $publisherComponent) use ($publisher, $token) {
                $publisherComponent->updatePublisherWithToken($publisher->getId(), [
                    'name' => 'New Name',
                ], $token->getToken());
            })
            ->assertStatus(201)
            ->assertJsonMatches('name', 'New Name');
    }
}
