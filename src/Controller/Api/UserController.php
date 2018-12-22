<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\Api\FailApiException;
use App\Repository\BookmarkArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/subscribe/{email}", name="api_user_subscribe")
     * @throws \Exception
     */
    public function subscribe(User $subscribeUser, Request $request, BookmarkArticleRepository $rep, PaginatorInterface $paginator)
    {
        /** @var User $followerUser */
        $followerUser = $this->getUser();
        try {
            if ($followerUser === $subscribeUser) {
                return $this->json([
                    'type' => 'error',
                    'message' => 'You cannot subscribe for yourself'
                ]);
            }

            if ($subscribeUser->getFollowers()->contains($followerUser)) {
                return $this->json([
                    'type' => 'info',
                    'message' => 'You were already subscribed to the author'
                ]);
            }

            $subscribeUser->addFollower($followerUser);
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscribeUser);
            $em->flush();

            return $this->json([
                'type' => 'success',
                'message' => 'You are subscribed to the author',
            ]);
        } catch (\Exception $e) {
            throw new FailApiException();
        }
    }

    /**
     * @IsGranted("UNSUBSCRIBE", subject="unsubscribeUser")
     * @Route("/unsubscribe/{email}", name="api_user_unsubscribe")
     * @throws \Exception
     */
    public function unsubscribe(User $unsubscribeUser, Request $request, BookmarkArticleRepository $rep, PaginatorInterface $paginator)
    {
        /** @var User $followerUser */
        $followerUser = $this->getUser();
        try {
            if ($followerUser === $unsubscribeUser) {
                return $this->json([
                    'type' => 'error',
                    'message' => 'You cannot unsubscribe from yourself'
                ]);
            }

            if (!$unsubscribeUser->getFollowers()->contains($followerUser)) {
                return $this->json([
                    'type' => 'info',
                    'message' => 'You are not subscribed to the author'
                ]);
            }

            $unsubscribeUser->removeFollower($followerUser);
            $em = $this->getDoctrine()->getManager();
            $em->persist($unsubscribeUser);
            $em->flush();

            return $this->json([
                'type' => 'success',
                'message' => 'You are succesfully unsubscribed',
            ]);
        } catch (\Exception $e) {
            throw new FailApiException();
        }
    }
}