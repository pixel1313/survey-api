<?php

namespace App\Security\Voter;

use App\Entity\Survey;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SurveyVoter extends Voter
{
    public const EDIT = 'EDIT';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof Survey;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        assert($subject instanceof Survey);

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if(!$this->security->isGranted('ROLE_SURVEY_EDIT')) {
                    return true;
                }

                if($subject->getOwner() === $user) {
                    return true;
                }

                break;
        }

        return false;
    }
}
