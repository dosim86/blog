<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use App\Exception\User\RegistrationException;
use App\Exception\User\UserMailingException;
use App\Lib\Helper;
use App\Lib\Traits\ManagerTrait;
use App\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserManager
{
    use ManagerTrait;

    private $mailer;

    public function __construct(ContainerInterface $container, MailerService $mailer)
    {
        $this->container = $container;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function registerUser(User $user)
    {
        $em = $this->getEntityManager();
        $userRepository = $em->getRepository(User::class);

        if ($userRepository->findOneBy(['email' => $user->getEmail()])) {
            throw new RegistrationException();
        }

        $user->setPassword($this->getEncodedPassword($user));
        $user->setActivateHash(Helper::randomHash32());
        $user->setFirstname($user->getUsername());

        $subject = 'Activate registration link';
        $message = $this->renderView('security/message/register_mail.html.twig', [
            'link' => $this->generateUrl('app_activate', [
                'activateHash' => $user->getActivateHash()
            ]),
        ]);
        $this->sendEmail($user->getEmail(), $subject, $message);

        $em->persist($user);
        $em->flush();
    }

    /**
     * @param $activateHash
     * @return bool
     * @throws \Exception
     */
    public function activateUser($activateHash)
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneBy(['activateHash' => $activateHash]);
        if (empty($user) || $user->isActivated()) {
            return false;
        }

        $user->setIsActivated(true);
        $em->persist($user);
        $em->flush();

        return true;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function resetUserPassword(User $user)
    {
        $user->setPlainPassword(substr(md5(random_bytes(32)), 0, 10));
        $user->setPassword($this->getEncodedPassword($user));

        $subject = 'Sending a new password';
        $message = $this->renderView('security/message/reset_password.html.twig', [
            'plain_password' => $user->getPlainPassword(),
            'link' => $this->generateUrl('app_login'),
        ]);
        $this->sendEmail($user->getEmail(), $subject, $message);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function rankUser(User $user)
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Article::class);

        /** @var ArticleRepository $repository */
        $totalCount = $repository->getTotalLikeCount($user);
        $user->setRank($totalCount);

        $em->persist($user);
        $em->flush();
    }

    /**
     * @param User $user
     */
    public function refreshUserLastActivity(User $user)
    {
        $user->refreshLastActivity();
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    private function getEncodedPassword(User $user)
    {
        return $this->getPasswordEncoder()->encodePassword($user, $user->getPlainPassword());
    }

    /**
     * @param $to
     * @param $subject
     * @param $text
     * @throws \Exception
     */
    private function sendEmail($to, $subject, $text)
    {
        $from = $this->getParameter('app.email_register');

        if (!$this->mailer->send([
            'subject' => $subject,
            'from' => $from,
            'to' => $to,
            'text' => $text
        ])) {
            throw new UserMailingException();
        }
    }
}