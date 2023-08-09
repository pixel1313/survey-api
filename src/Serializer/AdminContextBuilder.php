<?php

namespace App\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsDecorator('api_platform.serializer.context_builder')]
final class AdminContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if($resourceClass === Survey::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization) {
            $context['groups'][] = $normalization ? 'admin:read' : 'admin:write';
        }

        return $context;
    }
}