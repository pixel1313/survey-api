<?php

namespace App\Tests\Functional;

use App\Factory\ChoiceQuestionFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Json;
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
            return [
                'owner' => UserFactory::random(),
            ];
        });

        ChoiceQuestionFactory::createMany(10, function() {
            return [
                'survey' => SurveyFactory::random(),
            ];
        });

        ResponseQuestionFactory::createMany(10, function() {
            return [
                'survey' => SurveyFactory::random(),
            ];
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
}
