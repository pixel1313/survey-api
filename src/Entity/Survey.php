<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SurveyRepository::class)]
#[ApiResource]
class Survey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\OneToMany(mappedBy: 'survey', targetEntity: Question::class, orphanRemoval: true)]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'survey', targetEntity: SurveyResponse::class, orphanRemoval: true)]
    private Collection $surveyResponses;

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
}
