<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get('X-AUTH-TOKEN'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];

        if (null === $apiKey) {
            return;
        }

        $user = $this->userRepository->findOneBy(['apiKey' => $apiKey]);
        if (empty($user)) {
            throw new CustomUserMessageAuthenticationException('Wrong API key');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        /** @var User $user */
        if ($user->isDisabled()) {
            throw new CustomUserMessageAuthenticationException('Account is disabled');
        }

        if (!$user->isActivated()) {
            throw new CustomUserMessageAuthenticationException('Account is not activated. Please check your email');
        }

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}