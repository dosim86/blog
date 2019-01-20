<?php

namespace App\Subscriber;

use App\Event\UserEvent;
use App\Service\QueueService;
use App\Worker\UserWorker;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private $queueService;

    private $appLogger;

    public function __construct(QueueService $queueService, LoggerInterface $appLogger)
    {
        $this->queueService = $queueService;
        $this->appLogger = $appLogger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::RANK => 'onUserRank',
            UserEvent::ACTIVE => 'onUserActive',
            UserEvent::REGISTER => 'onUserRegister',
            UserEvent::RESET_PASSWORD => 'onUserResetPassword',
        ];
    }

    /**
     * @throws \Exception
     */
    public function onUserRank(UserEvent $event)
    {
        $this->queueService->addTask(UserWorker::RANK, [
            'userId' => $event->getUser()->getId()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function onUserActive(UserEvent $event)
    {
        $this->queueService->addTask(UserWorker::ACTIVE, [
            'userId' => $event->getUser()->getId()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function onUserRegister(UserEvent $event)
    {
        $this->queueService->addTask(UserWorker::REGISTER, [
            'user' => $event->getUser(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function onUserResetPassword(UserEvent $event)
    {
        $this->queueService->addTask(UserWorker::RESET_PASSWORD, [
            'userId' => $event->getUser()->getId()
        ]);
    }
}