<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidOwnerValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof IsValidOwner);

        if (null === $value || '' === $value) {
            return;
        }

        // constraint is only meant to be used above a User property
        assert($value instanceof User);

        $unitOfWork = $this->entityManager->getUnitOfWork();

        $originalData = $unitOfWork->getOriginalEntityData($value);
        $originalOwnerId = $originalData['id'];
        $newOwnerId = $value->getId();

        if(!$originalOwnerId || $originalOwnerId = $newOwnerId) {
            return;
        }

        $user = $this->security->getUser();
        if(!$user) {
            throw new \LogicException('IsOwnerValidator should only be used when a user is logged in.');
        }
        
        if($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if($value !== $user) {
            $this->context->buildViolation($constraint->message)
            ->addViolation();
        }
    }
}
