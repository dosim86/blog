<?php

namespace App\Subscriber;

use App\Entity\Article;
use App\Entity\User;
use App\Event\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::RANK => 'onUserRank',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function onUserRank(UserEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        $attributes->set(UserEvent::RANK, $event->getUserId());
    }

    /**
     * @param PostResponseEvent $event
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        if ($userId = $attributes->get(UserEvent::RANK, 0)) {
            $userRepository = $this->em->getRepository(User::class);
            $articleRepository = $this->em->getRepository(Article::class);

            /** @var User $user */
            if ($user = $userRepository->find($userId)) {
                $totalArticleLikeCount = $articleRepository->getTotalArticleLikeCountByAuthor($user);
                $user->setRank($totalArticleLikeCount);
                $this->em->persist($user);
                $this->em->flush();
            }
        }
    }
}