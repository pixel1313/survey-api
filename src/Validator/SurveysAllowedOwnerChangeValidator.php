<?php

namespace App\Validator;

use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SurveysAllowedOwnerChangeValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    )
    {}

    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof SurveysAllowedOwnerChange);

        if (null === $value || '' === $value) {
            return;
        }

        // meant to be used above a Collection field.
        assert($value instanceof Collection);

        $unitOfWork = $this->entityManager->getUnitOfWork();
        foreach($value as $survey) {
            assert($survey instanceof Survey);

            $originalData = $unitOfWork->getOriginalEntityData($survey);
            $originalOwnerId = $originalData['owner_id'];
            $newOwnerId = $survey->getOwner()->getId();

            if(!$originalOwnerId || $originalOwnerId === $newOwnerId) {
                return;
            }

            if($this->security->isGranted('ROLE_ADMIN')) {
                return;
            }
    
            // the owner is being changed
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
