<?php

namespace App\Security\Voter;

use App\Entity\Survey;
use Symfony\Bundle\SecurityBundle\Security;

class SurveyVoter extends AbstractVoter
{
    public const VIEW = 'ROLE_SURVEY_VIEW';
    public const EDIT = 'ROLE_SURVEY_EDIT';
    public const CREATE = 'ROLE_SURVEY_CREATE';
    public const DELETE = 'ROLE_SURVEY_DELETE';

    public function __construct(private Security $security)
    {
        parent::__construct($security, Survey::class);
    }

    protected function getPermissionNames(): array
    {
        return [
            static::EDIT,
            static::VIEW,
            static::CREATE,
            static::DELETE,
        ];
    }
}
