<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    public function __construct(
        private Security $security,
        private string $subjectClass
    ) {}

    protected abstract function getPermissionNames(): array;

    /**
     * Override this to detect ownership of the subject.
     */
    protected function isOwner(User $user, mixed $subject): bool {
        return false;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // only vote on our permissions
        if(!in_array($attribute, $this->getPermissionNames())) {
            return false;
        }

        // only vote on our subject class
        if($subject !== null && !($subject instanceof $this->subjectClass))
        {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject , TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // if the user is an admin, allow all actions.
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // if the user is the owner of the subject, allow all actions.
        if($this->isOwner($user, $subject)) {
            return true;
        }
        
        return $this->hasRoleByName($user, $attribute);
    }

    protected function hasRoleByName(User $user, string $roleName) {
        foreach($user->getRoles() as $role) {
            if($role === $roleName) {
                return true;
            }
        }

        return false;
    }
}