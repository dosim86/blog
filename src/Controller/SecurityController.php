<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserEvent;
use App\Form\RegisterType;
use App\Form\Filter\RestorePassFilter;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): Response
    {
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = new UserEvent($request, $user);
            $dispatcher->dispatch(UserEvent::REGISTER, $event);
            return $this->render('security/message/register_message.html.twig');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activate/{activateHash}", name="app_activate")
     * @throws \Exception
     */
    public function activate($activateHash, UserManager $userManager)
    {
        if (!$userManager->activateUser($activateHash)) {
            return $this->render('security/message/activate_fail_message.html.twig');
        }

        $this->addFlash('success', 'Your account is activated');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/restore", name="app_reset_password")
     */
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $form = $this->createForm(RestorePassFilter::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            if ($user = $userRepository->findOneBy(['email' => $email])) {
                $event = new UserEvent($request, $user);
                $dispatcher->dispatch(UserEvent::RESET_PASSWORD, $event);

                return $this->render('security/message/restore_message.html.twig');
            }

            $form->get('email')->addError(
                new FormError('Email not found')
            );
        }

        return $this->render('security/restore.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
