<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ChoiceAnswer extends Answer
{
    #[ORM\Column]
    private array $choices = [];

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): static
    {
        $this->choices = $choices;

        return $this;
    }
}