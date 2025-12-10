<?php

namespace App\Security\Voter;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CovoiturageVoter extends Voter
{
    public const MANAGE = 'COVOITURAGE_MANAGE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MANAGE && $subject instanceof Covoiturage;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$subject instanceof Covoiturage || !$user instanceof Utilisateur) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $owner = $subject->getVoiture()?->getProprietaire();
        return $owner && $owner->getId() === $user->getId();
    }
}
