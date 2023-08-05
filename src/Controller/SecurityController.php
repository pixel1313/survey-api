<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager) {

    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(IriConverterInterface $iriConverter, #[CurrentUser] $user = null): Response
    {
        if(!$user) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".',
            ], 401);
        }

        $apiToken = new ApiToken();
        $apiToken->setOwnedBy($user);
        $apiToken->setScopes($user->getRoles());
        $apiToken->setExpiresAt(new \DateTimeImmutable('+30 days'));

        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();

        $user->setToken($apiToken->getToken());
        
        /*
        return $this->json([
            'user' => $user,
            'token' => $apiToken->getToken(),
        ], 200, [ 'Location' => $iriConverter->getIriFromResource($user)]);
        */

        dump($user);
        
        return new Response($this->serializer->serialize($user, 'json', [ 'groups' => 'user:login']), 200, [
            'Location' => $iriConverter->getIriFromResource($user),
        ]);
        
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}