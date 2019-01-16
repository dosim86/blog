<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Event\UserEvent;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/list", name="api_user_list")
     * @throws \Exception
     */
    public function list(UserRepository $repository, SerializerInterface $serializer)
    {
        $data = $repository->findAll();
        return $this->json(
            $serializer->serialize($data, 'json', ['groups' => 'public'])
        );
    }

    /**
     * @Route("/subscribe/{username<[[:alnum:]]+>}", name="api_user_subscribe", options={"expose"=true})
     */
    public function subscribe(User $subscribeUser)
    {
        /** @var User $followerUser */
        $followerUser = $this->getUser();

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
    }

    /**
     * @IsGranted("PERM_UNSUBSCRIBE", subject="unsubscribeUser")
     * @Route("/unsubscribe/{username<[[:alnum:]]+>}", name="api_user_unsubscribe", options={"expose"=true})
     */
    public function unsubscribe(User $unsubscribeUser)
    {
        /** @var User $followerUser */
        $followerUser = $this->getUser();

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
//            'nodisplay' => true
        ]);
    }

    /**
     * @Route("/active", name="api_user_active", options={"expose"=true})
     */
    public function active(Request $request, EventDispatcherInterface $dispatcher)
    {
        $event = new UserEvent($request, $this->getUser());
        $dispatcher->dispatch(UserEvent::ACTIVE, $event);

        return $this->json([]);
    }
}