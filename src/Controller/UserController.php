<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\ArticleRepository;
use App\Repository\BookmarkArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
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
     * @Route("/", name="user_list")
     */
    public function list(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ) {
        $pagination = $paginator->paginate(
            $userRepository->getAuthors(),
            $request->get('page', 1),
            User::AUTHOR_ITEM
        );

        return $this->render('user/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("PERM_EDIT", subject="user")
     * @Route("/{username<[[:alnum:]]+>}/edit", name="user_edit")
     */
    public function edit(User $user, Request $request, FileUploader $uploader)
    {
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $form->get('uploadedFile')->getData()) {
                if ($cropCoords = $form->get('crop_coords')->getData()) {
                    $options = ['image' => true, 'crop_coords' => $cropCoords];
                }
                $user->setAvatar($uploader->upload($file, $options ?? []));
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Information about you is updated');
            return $this->redirectToRoute('user_profile', ['username' => $user->getUsername()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{username<[[:alnum:]]+>}", name="user_profile")
     */
    public function show(User $user)
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{username<[[:alnum:]]+>}/articles", name="user_articles")
     */
    public function articles(
        User $user,
        Request $request,
        ArticleRepository $rep,
        PaginatorInterface $paginator
    ) {
        $pagination = $paginator->paginate(
            $rep->getUserArticles($user),
            $request->get('page', 1),
            Article::ITEMS
        );

        return $this->render('user/articles.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{username<[[:alnum:]]+>}/comments", name="user_comments")
     */
    public function comments(
        User $user,
        Request $request,
        CommentRepository $rep,
        PaginatorInterface $paginator
    ) {
        $pagination = $paginator->paginate(
            $rep->getUserComments($user),
            $request->get('page', 1),
            Comment::ITEMS
        );

        return $this->render('user/comments.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{username<[[:alnum:]]+>}/bookmarks", name="user_bookmarks")
     */
    public function bookmarks(
        User $user,
        Request $request,
        BookmarkArticleRepository $rep,
        PaginatorInterface $paginator
    ) {
        $pagination = $paginator->paginate(
            $rep->getArticlesFromBookmarkByUser($user),
            $request->get('page', 1),
            Article::ITEMS
        );

        return $this->render('user/bookmarks.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{username<[[:alnum:]]+>}/subscribs", name="user_subscribs")
     */
    public function subscribs(User $user)
    {
        return $this->render('user/subscribs.html.twig', [
            'user' => $user,
            'subscribs' => $user->getSubscribs(),
        ]);
    }

    /**
     * @Route("/{username<[[:alnum:]]+>}/followers", name="user_followers")
     */
    public function followers(User $user)
    {
        return $this->render('user/followers.html.twig', [
            'user' => $user,
            'followers' => $user->getFollowers(),
        ]);
    }
}
