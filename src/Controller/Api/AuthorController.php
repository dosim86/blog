<?php

namespace App\Controller\Api;

use App\Exception\Api\ApiException;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/author")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/list", name="api_author_list", options={"expose"=true})
     * @throws \Exception
     */
    public function list(Request $request, UserRepository $repository)
    {
        try {
            $authorName = $request->get('q', '');
            $authors = $repository->getAuthorsMatchTo($authorName);

            return $this->json([
                'type' => 'success',
                'data' => $authors
            ]);
        } catch (\Exception $e) {
            throw new ApiException();
        }
    }
}