<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\ArticleRepository;
use App\Repository\BookmarkArticleRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("EDIT", subject="user")
     * @Route("/{email}/edit", name="user_edit")
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Information about you is updated');

            return $this->redirectToRoute('user_profile', ['email' => $user->getUsername()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{email}", name="user_profile")
     */
    public function index(User $user)
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{email}/articles", name="user_articles")
     */
    public function articles(User $user, Request $request, ArticleRepository $rep, PaginatorInterface $paginator)
    {
        $page = $request->query->getInt('page', 1);
        $qb = $rep->getUserArticles($user);
        $pagination = $paginator->paginate($qb, $page, 10);

        return $this->render('user/articles.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{email}/comments", name="user_comments")
     */
    public function comments(User $user, Request $request, CommentRepository $rep, PaginatorInterface $paginator)
    {
        $page = $request->query->getInt('page', 1);
        $qb = $rep->getUserComments($user);
        $pagination = $paginator->paginate($qb, $page, 10);

        return $this->render('user/comments.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{email}/bookmarks", name="user_bookmarks")
     */
    public function bookmarks(User $user, Request $request, BookmarkArticleRepository $rep, PaginatorInterface $paginator)
    {
        $page = $request->query->getInt('page', 1);
        $qb = $rep->getArticlesFromBookmarkByUser($user);
        $pagination = $paginator->paginate($qb, $page, 10);

        return $this->render('user/bookmarks.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{email}/subscribs", name="user_subscribs")
     */
    public function subscribs(User $user)
    {
        return $this->render('user/subscribs.html.twig', [
            'user' => $user,
            'subscribs' => $user->getSubscribs(),
        ]);
    }

    /**
     * @Route("/{email}/followers", name="user_followers")
     */
    public function followers(User $user)
    {
        return $this->render('user/followers.html.twig', [
            'user' => $user,
            'followers' => $user->getFollowers(),
        ]);
    }
}
