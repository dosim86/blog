<?php

namespace App\Controller\Api;

use App\Lib\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/util")
 */
class UtilController extends AbstractController
{
    /**
     * @Route("/token", name="api_util_token", options={"expose"=true})
     * @throws \Exception
     */
    public function token()
    {
        return $this->json(Helper::generateToken());
    }
}