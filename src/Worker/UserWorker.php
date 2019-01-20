<?php

namespace App\Worker;

use App\Command\Worker\WorkerInterface;
use App\Entity\User;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserWorker implements WorkerInterface
{
    const RANK = 'user.rank';
    const ACTIVE = 'user.active';
    const REGISTER = 'user.register';
    const RESET_PASSWORD = 'user.reset_password';

    private $em;

    private $logger;

    private $userManager;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $appLogger,
        UserManager $userManager
    ) {
        $this->em = $em;
        $this->logger = $appLogger;
        $this->userManager = $userManager;
    }

    public function getRegisterWorkers(): array
    {
        return [
            self::RANK => 'rank',
            self::ACTIVE => 'active',
            self::REGISTER => 'register',
            self::RESET_PASSWORD => 'resetPassword',
        ];
    }

    /**
     * @throws \Exception
     */
    public function rank(array $data)
    {
        try {
            $this->em->beginTransaction();

            $user = $this->getUserById($data['userId'], true);
            $this->userManager->rankUser($user);

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function active(array $data)
    {
        try {
            $this->em->beginTransaction();

            $user = $this->getUserById($data['userId'], true);
            $this->userManager->refreshUserLastActivity($user);

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function register(array $data)
    {
        $user = $data['user'];
        $this->userManager->registerUser($user);
    }

    /**
     * @throws \Exception
     */
    public function resetPassword(array $data)
    {
        $user = $this->getUserById($data['userId']);
        $this->userManager->resetUserPassword($user);
    }

    private function getUserById($userId, $lock = false)
    {
        return $this->em
            ->getRepository(User::class)
            ->getById($userId, $lock)
        ;
    }
}
