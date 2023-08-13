<?php

namespace App\Tests\Functional;

use App\Entity\ApiToken;
use App\Factory\ApiTokenFactory;
use App\Factory\ChoiceQuestionFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use App\Factory\UserFactory;
use App\Tests\Component\SurveyComponent;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Summary of SurveyResourceTest
 * 
 * @todo extract additional Content-Type information to the ApiTestClass.
 * 
 * @group Survey
 */
class SurveyResourceTest extends ApiTestCase
{
    use ResetDatabase;

    /**
     * Get a collection of surveys and verify that the right number come back and the format is correct.
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
            'owner' => $user,
            'isPublished' => true
        ]);

        SurveyFactory::createOne([
            'owner' => $user,
            'isPublished' => false,
        ]);

        ChoiceQuestionFactory::createMany(10, function() {
            return ['survey' => SurveyFactory::random()];
        });

        ResponseQuestionFactory::createMany(10, function() {
            return ['survey' => SurveyFactory::random()];
        });

        $this->browser()
            ->actingAs($user)
            ->get('api/surveys')
            ->use(function (SurveyComponent $surveyComponent) {
                $surveyComponent->assertCorrectHydraCollectionFormat(6);
            })
            ->assertJson();
    }

    /**
     * Get a 404 from accessing an unpublished survey.
     * @return void
     * 
     * @group GET
     * @group Errors
     */
    public function testGetOneUnpublishedSurvey404s(): void
    {
        $survey = SurveyFactory::createOne([
            'isPublished' => false,
        ]);

        $this->browser()
            ->get('/api/surveys/' . $survey->getId())
            ->assertStatus(404);
    }

    /**
     * Summary of testPostToCreateSurvey
     * @return void
     * 
     * @group POST
     * @group Security
     */
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

    /**
     * Summary of testPostToCreateSurveyWithApiKey
     * @return void
     * 
     * @group POST
     * @group Security
     */
    public function testPostToCreateSurveyWithApiKey(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_SURVEY_CREATE],
        ]);

        $this->browser()
            ->use(function (SurveyComponent $surveyComponent) use ($token) {
                $surveyComponent->postWithToken([], $token->getToken());
            })
            ->assertStatus(422)
        ;
    }

    /**
     * Summary of testPostToCreateSurveyDeniedWithoutScope
     * @return void
     * 
     * @group POST
     * @group Security
     */
    public function testPostToCreateSurveyDeniedWithoutScope(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_SURVEY_EDIT],
        ]);

        $this->browser()
            ->use(function (SurveyComponent $surveyComponent) use ($token) {
                $surveyComponent->postWithToken([], $token->getToken());
            })
            ->assertStatus(403)
        ;
    }

    /**
     * Summary of testPatchToUpdateSurvey
     * @return void
     * 
     * @group PATCH
     * @group Security
     */
    public function testPatchToUpdateSurvey(): void
    {
        $user = UserFactory::createOne();
        $survey = SurveyFactory::createOne(['owner' => $user]);

        $this->browser()
            ->actingAs($user)
            ->use(function (SurveyComponent $surveyComponent) use ($survey) {
                $surveyComponent->updateSurvey($survey->getId(), [
                    'name' => 'A different name',
                ]);
            })
            ->assertStatus(200)
            ->assertJsonMatches('name', 'A different name')
        ;

        $user2 = UserFactory::createOne();
        $this->browser()
            ->actingAs($user2)
            ->use(function (SurveyComponent $surveyComponent) use ($survey) {
                $surveyComponent->updateSurvey($survey->getId(), [
                    'name' => 'A third name',
                ]);
            })
            ->assertStatus(403)
        ;

        $this->browser()
            ->actingAs($user)
            ->use(function (SurveyComponent $surveyComponent) use ($survey, $user2) {
                $surveyComponent->updateSurvey($survey->getId(), [
                    'owner' => '/api/users/' . $user2->getId(),
                ]);
            })
            ->assertStatus(422)
        ;
    }

    /**
     * Summary of testPatchUnpublishedWorks
     * @return void
     * 
     * @group PATCH
     * @group Security
     */
    public function testPatchUnpublishedWorks()
    {
        $user = UserFactory::createOne();
        $survey = SurveyFactory::createOne([
            'owner' => $user,
            'isPublished' => false,
        ]);

        $this->browser()
            ->actingAs($user)
            ->use(function (SurveyComponent $surveyComponent) use ($survey) {
                $surveyComponent->updateSurvey($survey->getId(), [
                    'name' => 'A different name',
                ]);
            })
            ->assertStatus(200)
            ->assertJsonMatches('name', 'A different name')
        ;
    }

    /**
     * Summary of testAdminCanPatchToEditSurvey
     * @return void
     * 
     * @group PATCH
     * @group Security
     * @group Admin
     */
    public function testAdminCanPatchToEditSurvey(): void
    {
        $admin = UserFactory::new()->asAdmin()->create();
        $survey = SurveyFactory::createOne([
            'isPublished' => true,
        ]);

        $this->browser()
            ->actingAs($admin)
            ->use(function (SurveyComponent $surveyComponent) use ($survey) {
                $surveyComponent->updateSurvey($survey->getId(), [
                    'name' => 'Admin rename',
                ]);
            })
            ->assertStatus(200)
            ->assertJsonMatches('name', 'Admin rename')
            ->assertJsonMatches('isPublished', true)
        ;
    }

    /**
     * Summary of testOwnerCanSeeIsPublishedField
     * @return void
     * 
     * @group PATCH
     * @group Security
     * @group Owner
     */
    public function testOwnerCanSeeIsPublishedField(): void
    {
        $user = UserFactory::new()->create();
        $survey = SurveyFactory::createOne([
            'isPublished' => true,
            'owner' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->use(function (SurveyComponent $surveyComponent) use ($survey) {
                $surveyComponent->updateSurvey($survey->getId(), [
                    'name' => 'new name',
                ]);
            })
            ->assertStatus(200)
            ->assertJsonMatches('name', 'new name')
            ->assertJsonMatches('isPublished', true)
        ;
    }
}
