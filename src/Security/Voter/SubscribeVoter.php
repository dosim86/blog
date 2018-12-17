<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscribeVoter extends Voter
{
    const UNSUBSCRIBE = 'UNSUBSCRIBE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::UNSUBSCRIBE])
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
            case self::UNSUBSCRIBE:
                if ($user->getSubscribs()->contains($unsubscribeUser)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
