<?php

namespace App\DataFixtures;

use App\Entity\ChoiceQuestion;
use App\Factory\ChoiceQuestionFactory;
use App\Factory\ResponseQuestionFactory;
use App\Factory\SurveyFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        SurveyFactory::createMany(20);

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
    }
}
