<?php

namespace App\DataFixtures;

use App\Entity\ChoiceQuestion;
use App\Factory\ApiTokenFactory;
use App\Factory\ChoiceQuestionFactory;
use App\Factory\PublisherFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PublisherFactory::createMany(5);
        UserFactory::createMany(10, function () {
            return [
                'publisher' => PublisherFactory::random(),
            ];
        });
        
        SurveyFactory::createMany(20, function() {
            return [
                'publisher' => PublisherFactory::random(),
                'isPublished' => rand(0, 10) > 3,
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
