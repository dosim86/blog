<?php

namespace App\Subscriber;

use App\Exception\Api\FailApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ApiSubscriber implements EventSubscriberInterface
{
    private $security;

    private $publicApi = [
        '/api/author/list'
    ];

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

        if (!$this->security->isGranted('ROLE_USER') && !$this->isPublicApi($event->getRequest())) {
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