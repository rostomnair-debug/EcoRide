<?php

namespace App\Security\Voter;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UtilisateurVoter extends Voter
{
    public const SUSPEND = 'USER_SUSPEND';
    public const PROMOTE = 'USER_PROMOTE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::SUSPEND, self::PROMOTE], true) && $subject instanceof Utilisateur;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$subject instanceof Utilisateur || !$user instanceof Utilisateur) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
