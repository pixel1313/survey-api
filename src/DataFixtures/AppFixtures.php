<?php

namespace App\DataFixtures;

use App\Entity\ChoiceQuestion;
use App\Factory\ApiTokenFactory;
use App\Factory\ChoiceQuestionFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        
        SurveyFactory::createMany(20, function() {
            return [
                'owner' => UserFactory::random(),
            ];
        });

        ChoiceQuestionFactory::createMany(40, function() {
            return [
                'survey' => SurveyFactory::random(),
            ];
        });

        ResponseQuestionFactory::createMany(40, function() {
            return [
                'survey' => SurveyFactory::random(),
            ];
        });

        ApiTokenFactory::createMany(30, function() {
            return [
                'ownedBy' => UserFactory::random(),
            ];
        });
    }
}
