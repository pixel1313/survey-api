<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SurveyRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['survey:read']],
    denormalizationContext: ['groups' => ['survey:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_SURVEY_CREATE")',
        ),
        new Put(
            security: 'is_granted("ROLE_SURVEY_EDIT")',
        ),
        new Patch(
            security: 'is_granted("ROLE_SURVEY_EDIT")',
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
        ),
    ]
)]
class Survey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        maxMessage: "Name your survey in 255 characters or less"
    )]
    #[Groups(['survey:read', 'survey:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['survey:read', 'survey:write'])]
    private ?bool $isPublished = null;

    #[ORM\OneToMany(mappedBy: 'survey', targetEntity: Question::class, orphanRemoval: true)]
    #[Groups(['survey:read', 'survey:write'])]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'survey', targetEntity: SurveyResponse::class, orphanRemoval: true)]
    #[Groups(['survey:read', 'survey:write'])]
    private Collection $surveyResponses;

    #[ORM\ManyToOne(inversedBy: 'surveys')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Groups(['survey:read', 'survey:write'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->surveyResponses = new ArrayCollection();
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

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setSurvey($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getSurvey() === $this) {
                $question->setSurvey(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SurveyResponse>
     */
    public function getSurveyResponses(): Collection
    {
        return $this->surveyResponses;
    }

    public function addSurveyResponse(SurveyResponse $surveyResponse): static
    {
        if (!$this->surveyResponses->contains($surveyResponse)) {
            $this->surveyResponses->add($surveyResponse);
            $surveyResponse->setSurvey($this);
        }

        return $this;
    }

    public function removeSurveyResponse(SurveyResponse $surveyResponse): static
    {
        if ($this->surveyResponses->removeElement($surveyResponse)) {
            // set the owning side to null (unless already changed)
            if ($surveyResponse->getSurvey() === $this) {
                $surveyResponse->setSurvey(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
