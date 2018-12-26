<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Event\UserEvent;
use App\Service\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserSubscriber implements EventSubscriberInterface
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::RANK => 'onUserRank',
            UserEvent::REGISTER => 'onUserRegister',
            UserEvent::RESET_PASSWORD => 'onUserResetPassword',
            UserEvent::ACTIVE => 'onUserActive',
            KernelEvents::FINISH_REQUEST => 'onKernelFinishRequest',
        ];
    }

    public function onUserRank(UserEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        $attributes->set(UserEvent::RANK, $event->getUser());
    }

    public function onUserRegister(UserEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        $attributes->set(UserEvent::REGISTER, $event->getUser());
    }

    public function onUserResetPassword(UserEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        $attributes->set(UserEvent::RESET_PASSWORD, $event->getUser());
    }

    public function onUserActive(UserEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        $attributes->set(UserEvent::ACTIVE, $event->getUser());
    }

    /**
     * @param FinishRequestEvent $event
     * @throws \Exception
     */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        /** @var User $user */
        $attributes = $event->getRequest()->attributes;

        if ($user = $attributes->get(UserEvent::RANK, null)) {
            $this->userManager->rankUser($user);
        }

        if ($user = $attributes->get(UserEvent::REGISTER, null)) {
            $this->userManager->registerUser($user);
        }

        if ($user = $attributes->get(UserEvent::RESET_PASSWORD, null)) {
            $this->userManager->resetUserPassword($user);
        }

        if ($user = $attributes->get(UserEvent::ACTIVE, null)) {
            $this->userManager->refreshUserLastActivity($user);
        }
    }
}