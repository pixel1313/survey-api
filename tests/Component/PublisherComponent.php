<?php

namespace App\Tests\Component;

use Zenstruck\Browser\Component;

/**
 * Helper functions for Publisher tests.
 * 
 * @method KernelBrowser browser()
 */
class PublisherComponent extends Component
{
    const API_URL = '/api/publishers';

    public function getPublisherCollectionWithToken(string $token): self
    {
        $this->browser()->get(self::API_URL, [
            'headers' => ['Authorization' => 'Bearer ' . $token],
        ]);

        return $this;
    }

    public function getPublisherItemWithToken(int $id, string $token): self
    {
        $this->browser()
            ->get(self::API_URL . '/' . $id, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);

        return $this;
    }

    public function createWithToken(array $jsonPublisher, string $token): self
    {
        $this->browser()
            ->post(self::API_URL, [
                'json' => $jsonPublisher,
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);

        return $this;
    }

    public function updatePublisherWithToken(int $id, array $jsonPublisher, string $token): self
    {
        $this->browser()
            ->patch(self::API_URL . '/' . $id, [
                'json' => $jsonPublisher,
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

        return $this;
    }
}