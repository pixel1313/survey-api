<?php

namespace App\Tests\Functional;

use App\Entity\ApiToken;
use App\Factory\ApiTokenFactory;
use App\Factory\ChoiceQuestionFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\ResetDatabase;

class SurveyResourceTest extends ApiTestCase
{
    use ResetDatabase;

    /**
     * Summary of testGetCollectionOfSurveys
     * @return void
     * 
     * @group GET
     * @group SurveyCollection
     * @group json
     */
    public function testGetCollectionOfSurveys(): void
    {
        $user = UserFactory::createOne();
        
        SurveyFactory::createMany(5, [
            'owner' => UserFactory::random(),
            'isPublished' => true
        ]);

        SurveyFactory::createOne([
            'owner' => UserFactory::random(),
            'isPublished' => false,
        ]);

        ChoiceQuestionFactory::createMany(10, function() {
            return ['survey' => SurveyFactory::random()];
        });

        ResponseQuestionFactory::createMany(10, function() {
            return ['survey' => SurveyFactory::random()];
        });

        $json = $this->browser()
            ->actingAs($user)
            ->get('api/surveys')
            ->assertJson()
            ->assertJsonMatches('"hydra:totalItems"', 6)
            ->assertJsonMatches('length("hydra:member")', 6)
            ->json()
        ;

        $json->assertMatches('keys("hydra:member"[0])', [
            '@id',
            '@type',
            'id',
            'name',
            'isPublished',
            'owner',
        ]);
    }

    public function testGetOneUnpublishedSurvey404s(): void
    {
        $survey = SurveyFactory::createOne([
            'isPublished' => false,
        ]);

        $this->browser()
            ->get('/api/surveys/' . $survey->getId())
            ->assertStatus(404);
    }

    public function testPostToCreateSurvey(): void
    {
        $user = UserFactory::createOne(['password' => 'pass']);

        $this->browser()
            ->post('/login', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'pass',
                ],
            ])
            ->assertStatus(200)
            ->post('/api/surveys', [
                'json' => [],
            ])
            ->assertStatus(422)
            ->post('/api/surveys', [
                'json' => [
                    'name' => 'Testing Survey',
                    'isPublished' => true,
                ],
            ])
            ->assertStatus(201)
            ->assertJsonMatches('name', 'Testing Survey')
        ;
    }

    public function testPostToCreateSurveyWithApiKey(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_SURVEY_CREATE],
        ]);

        $this->browser()
            ->post('/api/surveys', [
                'json' => [],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token->getToken(),
                ],
            ])
            ->assertStatus(422)
        ;
    }

    public function testPostToCreateSurveyDeniedWithoutScope(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_SURVEY_EDIT],
        ]);

        $this->browser()
            ->post('/api/surveys', [
                'json' => [],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token->getToken(),
                ],
            ])
            ->assertStatus(403)
        ;
    }

    /**
     * @todo Make sure the Content-Type issue isn't a bigger problem with
     * configuration.
     */
    public function testPatchToUpdateSurvey(): void
    {
        $user = UserFactory::createOne();
        $survey = SurveyFactory::createOne(['owner' => $user]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/surveys/' . $survey->getId(), [
                'json' => [
                    'name' => 'A different Name',
                ],
                'headers' => [
                    'Content-Type' => "application/merge-patch+json",
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('name', 'A different Name')
        ;

        $user2 = UserFactory::createOne();
        $this->browser()
            ->actingAs($user2)
            ->patch('/api/surveys/' . $survey->getId(), [
                'json' => [
                    'name' => 'A third name',
                ],
                'headers' => [
                    'Content-Type' => "application/merge-patch+json",
                ]
            ])
            ->dump()
            ->assertStatus(403)
        ;
        
        $this->browser()
            ->actingAs($user)
            ->patch('/api/surveys/' . $survey->getId(), [
                'json' => [
                    'owner' => '/api/users/' . $user2->getId(),
                ],
                'headers' => [
                    'Content-Type' => "application/merge-patch+json",
                ]
            ])
            ->assertStatus(422)
        ;
    }

    public function testPatchUnpublishedWorks()
    {
        $user = UserFactory::createOne();
        $survey = SurveyFactory::createOne([
            'owner' => $user,
            'isPublished' => false,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/surveys/' . $survey->getId(), [
                'json' => [
                    'name' => 'A different Name',
                ],
                'headers' => [
                    'Content-Type' => "application/merge-patch+json",
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('name', 'A different Name')
        ;
    }

    public function testAdminCanPatchToEditSurvey(): void
    {
        $admin = UserFactory::new()->asAdmin()->create();
        $survey = SurveyFactory::createOne([
            'isPublished' => true,
        ]);

        $this->browser()
            ->actingAs($admin)
            ->patch('/api/surveys/' . $survey->getId(), [
                'json' => [
                    'name' => 'Admin rename',
                ],
                'headers' => [
                    'Content-Type' => "application/merge-patch+json",
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('name', 'Admin rename')
            ->assertJsonMatches('isPublished', true)
        ;
    }

    public function testOwnerCanSeeIsPublishedField(): void
    {
        $user = UserFactory::new()->create();
        $survey = SurveyFactory::createOne([
            'isPublished' => true,
            'owner' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/surveys/' . $survey->getId(), [
                'json' => [
                    'name' => 'new name',
                ],
                'headers' => [
                    'Content-Type' => "application/merge-patch+json",
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('name', 'new name')
            ->assertJsonMatches('isPublished', true)
        ;
    }
}
