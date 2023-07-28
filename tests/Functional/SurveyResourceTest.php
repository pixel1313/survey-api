<?php

namespace App\Tests\Functional;

use App\Factory\ChoiceQuestionFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class SurveyResourceTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testGetCollectionOfSurveys(): void
    {
        UserFactory::createOne();
        
        SurveyFactory::createMany(5, function() {
            return ['owner' => UserFactory::random()];
        });

        ChoiceQuestionFactory::createMany(10, function() {
            return ['survey' => SurveyFactory::random()];
        });

        ResponseQuestionFactory::createMany(10, function() {
            return ['survey' => SurveyFactory::random()];
        });

        $json = $this->browser()
            ->get('api/surveys')
            ->assertJson()
            ->assertJsonMatches('"hydra:totalItems"', 5)
            ->assertJsonMatches('length("hydra:member")', 5)
            ->json()
        ;

        $json->assertMatches('keys("hydra:member"[0])', [
            '@id',
            '@type',
            'name',
            'isPublished',
            'questions',
            'surveyResponses',
            'owner',
        ]);
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
            ->assertStatus(204)
            ->post('/api/surveys', [
                'json' => [],
            ])
            ->assertStatus(422)
            ->post('/api/surveys', HttpOptions::json([
                'name' => 'Testing Survey',
                'isPublished' => true,
                'owner' => 'api/users/' . $user->getId(),
            ])->withHeader('Accept', 'application/ld+json'))
            ->assertStatus(201)
            ->dump()
            ->assertJsonMatches('name', 'Testing Survey')
        ;
    }
}
