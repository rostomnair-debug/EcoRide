<?php

namespace App\Security\Voter;

use App\Entity\Avis;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AvisVoter extends Voter
{
    public const MODERATE = 'AVIS_MODERATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MODERATE && $subject instanceof Avis;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$subject instanceof Avis || !$user instanceof Utilisateur) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_EMPLOYE', $user->getRoles(), true);
    }
}
