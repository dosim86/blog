<?php

namespace App\Listener;

use App\Exception\Api\FailApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ApiSubscriber implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    private function supports(KernelEvent $event)
    {
        $request = $event->getRequest();

        return $event->isMasterRequest()
            && $request->isXmlHttpRequest()
            && false !== strpos($request->getRequestUri(), '/api/')
        ;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->supports($event)) {
            return;
        }

        if (!$this->security->isGranted('ROLE_USER')) {
            throw new NotFoundHttpException();
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->supports($event)) {
            return;
        }

        if ($event->getException() instanceof FailApiException) {
            $response = new JsonResponse([
                'type' => 'error',
                'message' => 'Sorry, there is a system fault'
            ], 500);
            $event->setResponse($response);
        }
    }
}