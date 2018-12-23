<?php

namespace App\Subscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $flash;

    public function __construct(FlashBagInterface $flash)
    {
        $this->flash = $flash;
    }

    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_PERSIST => ['onEntityPostAdd'],
            EasyAdminEvents::POST_UPDATE => ['onEntityPostUpdate'],
            EasyAdminEvents::POST_DELETE=> ['onEntityPostDelete'],
        ];
    }

    public function onEntityPostAdd(GenericEvent $event)
    {
        $this->flash->add('success', $this->getEntityName($event).' is added');
    }

    public function onEntityPostUpdate(GenericEvent $event)
    {
        $this->flash->add('success', $this->getEntityName($event).' is updated');
    }

    public function onEntityPostDelete(GenericEvent $event)
    {
        $this->flash->add('success', $this->getEntityName($event).' is deleted');
    }

    private function getEntityName(GenericEvent $event)
    {
        $entityArr = explode('\\', get_class($event->getSubject()));
        return array_pop($entityArr);
    }
}