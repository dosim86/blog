<?php
/**
 * @author: dosim <misstilda@yandex.ru>
 */

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Route("/blog", name="blog_index")
     */
    public function index(Request $request, BlogRepository $blogRepository, PaginatorInterface $paginator)
    {
        $queryBuilder = $blogRepository->getWithQueryBuilder();
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);

        return $this->render('blog/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("blog/add", name="blog_add")
     */
    public function add(Request $request)
    {
        $blog = new Blog();

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute('blog_show', ['slug' => $blog->getSlug()]);
        }

        return $this->render('blog/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create new blog'
        ]);
    }

    /**
     * @Route("blog/edit/{id}", name="blog_edit")
     */
    public function edit(Blog $blog, Request $request)
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('blog_show', ['slug' => $blog->getSlug()]);
        }

        return $this->render('blog/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Update: ' . $blog->getTitle()
        ]);
    }

    /**
     * @Route("blog/{slug}", name="blog_show")
     */
    public function show(Blog $blog)
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog
        ]);
    }
}
