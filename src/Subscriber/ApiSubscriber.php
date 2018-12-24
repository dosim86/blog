<?php

namespace App\Subscriber;

use App\Exception\Api\FailApiException;
use App\Exception\Api\InvalidTokenApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ApiSubscriber implements EventSubscriberInterface
{
    private $publicApi = [
        '/api/author/list'
    ];

    private $security;

    private $csrfTokenManager;

    public function __construct(Security $security, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->security = $security;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    /**
     * @param GetResponseEvent $event
     * @throws InvalidTokenApiException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->supports($event)) {
            return;
        }

        if (!$this->isCsrfTokenValid($event->getRequest())) {
            throw new InvalidTokenApiException();
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
        $exception = $event->getException();

        if ($exception instanceof FailApiException) {
            $response = new JsonResponse([
                'type' => 'error',
                'message' => $exception->getMessage()
            ], 500);
            $event->setResponse($response);
        }

        if ($exception instanceof InvalidTokenApiException) {
            $response = new JsonResponse([
                'type' => 'error',
                'message' => $exception->getMessage()
            ], 400);
            $event->setResponse($response);
        }
    }

    private function supports(KernelEvent $event)
    {
        $request = $event->getRequest();

        if (false !== strpos($request->getRequestUri(), '/api/')) {
            if (!$event->isMasterRequest() || !$request->isXmlHttpRequest()) {
                throw new NotFoundHttpException();
            }
            return true;
        }

        return false;
    }

    private function isCsrfTokenValid(Request $request)
    {
        return $this->csrfTokenManager->isTokenValid(
            new CsrfToken('_api', $request->get('token'))
        );
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