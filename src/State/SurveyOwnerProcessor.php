<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Survey;
use Symfony\Bundle\SecurityBundle\Security;

class SurveyOwnerProcessor implements ProcessorInterface
{
    public function __construct(
        private PersistProcessor $innerProcessor,
        private Security $security
    )
    {}
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Survey
    {
        if($data instanceof Survey &&
            $data->getOwner() === null &&
            $this->security->getUser()
        ) {
            $data->setOwner($this->security->getUser());
        }

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
