<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute($attribute, $article, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:

                /** @var Article $article */
                return true;
                break;
        }

        return false;
    }
}
