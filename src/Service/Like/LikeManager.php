<?php

namespace App\Service\Like;

use App\Entity\User;
use App\Exception\AppException;
use App\Exception\Like\FailLikeException;
use App\Exception\Like\NotFoundLikeClassException;
use App\Exception\Like\UnknownLikeClassException;
use App\Exception\Like\UnsupportActionException;
use Doctrine\ORM\EntityManagerInterface;

class LikeManager
{
    private const LIKE = 1;
    private const DISLIKE = -1;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param LikeableInterface $entity
     * @param User $user
     * @throws AppException
     */
    public function like(LikeableInterface $entity, User $user)
    {
        $this->handle($entity, $user, self::LIKE);
    }

    /**
     * @param LikeableInterface $entity
     * @param User $user
     * @throws AppException
     */
    public function dislike(LikeableInterface $entity, User $user)
    {
        $this->handle($entity, $user, self::DISLIKE);
    }

    /**
     * @param LikeableInterface $entity
     * @param User $user
     * @param $action
     * @throws FailLikeException
     * @throws NotFoundLikeClassException
     * @throws UnknownLikeClassException
     * @throws UnsupportActionException
     */
    private function handle(LikeableInterface $entity, User $user, $action)
    {
        if (!$this->support($action)) {
            throw new UnsupportActionException();
        }

        if (empty($likeClass = $entity::getLikeClass())) {
            throw new NotFoundLikeClassException();
        }

        if (!is_subclass_of($likeClass, LikeInterface::class)) {
            throw new UnknownLikeClassException();
        }

        /** @var LikeInterface $like */
        $like = $this->em->getRepository($likeClass)->findOneBy([
            'userId' => $user->getId(),
            'targetId' => $entity->getId(),
        ]);

        try {
            if ($like) {
                if ($like->getValue() === $action) {
                    $this->em->remove($like);

                    $action === self::LIKE ? $entity->decLikeCount() : $entity->decDislikeCount();
                    $this->em->persist($entity);
                } else {
                    $like->setValue($action);
                    $this->em->persist($like);

                    if ($action === self::LIKE) {
                        $entity->decDislikeCount();
                        $entity->incLikeCount();
                    } else {
                        $entity->decLikeCount();
                        $entity->incDislikeCount();
                    }
                    $this->em->persist($entity);
                }
            } else {
                $like = new $likeClass;
                $like->setUserId($user->getId());
                $like->setTargetId($entity->getId());
                $like->setValue($action);
                $this->em->persist($like);

                $action === self::LIKE ? $entity->incLikeCount() : $entity->incDislikeCount();
                $this->em->persist($entity);
            }
            $this->em->flush();
        } catch (AppException $e) {
            throw new FailLikeException();
        }
    }

    /**
     * @param $action
     * @return bool
     */
    private function support($action)
    {
        return in_array($action, [
            self::LIKE,
            self::DISLIKE,
        ], true);
    }
}