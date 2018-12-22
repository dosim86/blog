<?php

namespace App\Entity;

use App\Service\Like\AbstractLike;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeCommentRepository")
 */
class LikeComment extends AbstractLike
{

}
