<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    private const PERSONAL_ACCESS_TOKEN_PREFIX = 'tcp_';

    // users
    public const SCOPE_USER_VIEW = 'ROLE_USER_VIEW';
    public const SCOPE_USER_CREATE = 'ROLE_USER_CREATE';
    public const SCOPE_USER_EDIT = 'ROLE_USER_EDIT';
    public const SCOPE_USER_DELETE = 'ROLE_USER_DELETE';

    // publishers
    public const SCOPE_PUBLISHER_VIEW = 'ROLE_PUBLISHER_VIEW';
    public const SCOPE_PUBLISHER_CREATE = 'ROLE_PUBLISHER_CREATE';
    public const SCOPE_PUBLISHER_EDIT = 'ROLE_PUBLISHER_EDIT';
    public const SCOPE_PUBLISHER_DELETE = 'ROLE_PUBLISHER_DELETE';

    // surveys
    public const SCOPE_SURVEY_VIEW = 'ROLE_SURVEY_VIEW';
    public const SCOPE_SURVEY_CREATE = 'ROLE_SURVEY_CREATE';
    public const SCOPE_SURVEY_EDIT = 'ROLE_SURVEY_EDIT';
    public const SCOPE_SURVEY_DELETE = 'ROLE_SURVEY_DELETE';

    public const SCOPES = [
        self::SCOPE_USER_VIEW => 'View User',
        self::SCOPE_USER_CREATE => 'Create User',
        self::SCOPE_USER_EDIT => 'Edit User',
        self::SCOPE_USER_DELETE => 'Delete User',

        self::SCOPE_SURVEY_VIEW => 'View Survey',
        self::SCOPE_SURVEY_CREATE => 'Create Survey',
        self::SCOPE_SURVEY_EDIT => 'Edit Survey',
        self::SCOPE_SURVEY_DELETE => 'Delete Survey',
        
        self::SCOPE_PUBLISHER_VIEW => 'View Publisher',
        self::SCOPE_PUBLISHER_CREATE => 'Create Publisher',
        self::SCOPE_PUBLISHER_EDIT => 'Edit Publisher',
        self::SCOPE_PUBLISHER_DELETE => 'Delete Publisher',
        
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'apiTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ownedBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(length: 68)]
    private ?string $token = null;

    #[ORM\Column]
    private array $scopes = [];

    public function __construct(string $tokenType = self::PERSONAL_ACCESS_TOKEN_PREFIX)
    {
        $this->token = $tokenType . bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnedBy(): ?User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?User $ownedBy): static
    {
        $this->ownedBy = $ownedBy;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->expiresAt === null || $this->expiresAt > new \DateTimeImmutable();
    }
}
