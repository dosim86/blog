<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class ApiSubscriber implements EventSubscriberInterface
{
    private $publicApi = [
        '/api/author/list',
        '/api/util/token',
    ];

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->supports($event) || $this->isPublicApi($request)) {
            return;
        }

        if (!$request->headers->has('X-AUTH-TOKEN') || !$this->security->isGranted('ROLE_USER')) {
            throw new AuthenticationException();
        }
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    private function supports(KernelEvent $event)
    {
        $request = $event->getRequest();

        if (false !== strpos($request->getRequestUri(), '/api/')) {
            if (!$event->isMasterRequest()) {
                throw new NotFoundHttpException();
            }
            return true;
        }

        return false;
    }

    private function isPublicApi(Request $request)
    {
        foreach ($this->publicApi as $api) {
            if (false !== strpos($request->getRequestUri(), $api)) {
                return true;
            }
        }
        return false;
    }
}