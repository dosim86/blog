<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscribeVoter extends Voter
{
    const PERM_UNSUBSCRIBE = 'PERM_UNSUBSCRIBE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::PERM_UNSUBSCRIBE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $unsubscribeUser, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::PERM_UNSUBSCRIBE:
                if ($user->getSubscribs()->contains($unsubscribeUser)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
