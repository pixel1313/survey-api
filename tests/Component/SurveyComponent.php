<?php

namespace App\Tests\Component;

use App\Entity\ApiToken;
use Zenstruck\Browser\Component;

/**
 * Helper functions for Survey tests.
 * 
 * @method KernelBrowser browser()
 */
class SurveyComponent extends Component
{
    /**
     * API URL for Surveys.
     * @var string
     * 
     * @todo pull this value from the environment.
     */
    const API_URL = '/api/surveys';

    public function updateSurvey(int $id, array $jsonSurvey): self
    {
        $this->browser()
            ->patch(self::API_URL . '/' . $id, [
                'json' => $jsonSurvey,
                'headers' => ['Content-Type' => "application/merge-patch+json",]
            ]);

        return $this;
    }

    public function postWithToken($json, string $token): self
    {
        $this->browser()
            ->post(self::API_URL, [
                'json' => $json,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

        return $this;
    }

    /**
     * Summary of assertCorrectHydraCollectionFormat
     * @param int $length
     * @return \App\Tests\Component\SurveyComponent
     */
    public function assertCorrectHydraCollectionFormat(int $length): self
    {
        $this->browser()
            ->assertJson()
            ->assertJsonMatches('"hydra:totalItems"', 6)
            ->assertJsonMatches('length("hydra:member")', 6)
            ->assertJsonMatches('keys("hydra:member"[0])', [
                '@id',
                '@type',
                'id',
                'name',
                'isPublished',
                'owner',
            ]);

        return $this;
    }
}