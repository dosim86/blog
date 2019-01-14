<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Exception\InvalidTokenException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $appLogger;

    private $security;

    public function __construct(LoggerInterface $appLogger, Security $security)
    {
        $this->appLogger = $appLogger;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $ex = $event->getException();
        $logPrefix = '';

        if ($this->isApiRequest($event)) {
            if ($ex instanceof InvalidTokenException) {
                $response = new JsonResponse([
                    'type' => 'error',
                    'message' => $ex->getMessage()
                ], Response::HTTP_BAD_REQUEST);

                $event->setResponse($response);
            } else {
                $response = new JsonResponse([
                    'type' => 'error',
                    'message' => 'Sorry, there is a system fault'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);

                $event->setResponse($response);
                $logPrefix = 'API: ';
            }
        }

        $this->appLogger->error($logPrefix . $ex->getMessage(), [
            'code' => $ex->getCode(),
            'line' => $ex->getLine(),
            'file' => $ex->getFile(),
            'user' => $this->getUserInfo()
        ]);
    }

    private function isApiRequest(KernelEvent $event)
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

    private function getUserInfo()
    {
        /** @var User $user */
        return ($user = $this->security->getUser()) ? $user->getId() . ':' . $user->getUsername() : '';
    }
}