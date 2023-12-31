<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['publisher:read']],
    denormalizationContext: ['groups' => ['publisher:write']],
    operations: [
        new Get(
            normalizationContext: ['gropus' => ['publisher:read', 'publisher:item:get']],
        ),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    extraProperties: [
        'standard_put' => true,
    ],
)]
class Publisher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['publisher:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['publisher:read', 'publisher:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        minMessage: 'Publisher name must be at least 2 characters long',
        max: 255,
        maxMessage: 'Publisher name must be less than 255 characters'
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: Survey::class)]
    #[Groups(['publisher:item:get', 'publisher:write'])]
    private Collection $surveys;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: User::class)]
    #[Groups(['publisher:item:get', 'publisher:write'])]
    private Collection $users;

    public function __construct()
    {
        $this->surveys = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Survey>
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurvey(Survey $survey): static
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys->add($survey);
            $survey->setPublisher($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): static
    {
        if ($this->surveys->removeElement($survey)) {
            // set the owning side to null (unless already changed)
            if ($survey->getPublisher() === $this) {
                $survey->setPublisher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setPublisher($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPublisher() === $this) {
                $user->setPublisher(null);
            }
        }

        return $this;
    }
}
