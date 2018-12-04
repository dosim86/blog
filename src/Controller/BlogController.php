<?php
/**
 * @author: dosim <misstilda@yandex.ru>
 */

namespace App\Controller;

use App\Entity\Blog;
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
     * @Route("blog/{slug}", name="blog_show")
     */
    public function show(Blog $blog)
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog
        ]);
    }
}
