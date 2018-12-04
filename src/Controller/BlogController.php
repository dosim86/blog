<?php
/**
 * @author: dosim <misstilda@yandex.ru>
 */

namespace App\Controller;

use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(BlogRepository $blogRepository)
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll()
        ]);
    }
}
