<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    const PERM_EDIT = 'PERM_EDIT';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::PERM_EDIT])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute($attribute, $article, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::PERM_EDIT:
                if ($article->getAuthor() === $user) {
                    return true;
                }

                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
        }

        return false;
    }
}
