<?php

namespace App\Entity;

use App\Service\Like\AbstractLike;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeArticleRepository")
 */
class LikeArticle extends AbstractLike
{

}
