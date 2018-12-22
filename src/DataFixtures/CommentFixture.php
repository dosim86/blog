<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixture extends BaseFixture implements DependentFixtureInterface
{
    const REF_NAME = 'comment';

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(90, self::REF_NAME, function ($index) use ($manager) {
            /** @var Article $article */
            $article = $this->getRandomReference(ArticleFixtures::REF_NAME);
            /** @var User $user */
            $user = $this->getRandomReference(UserFixture::REF_NAME);

            $comment = new Comment();
            $comment->setText($this->faker->words(10, true));
            $comment->setCreatedAt($this->faker->dateTimeBetween('-30 days'));
            $comment->setOwner($user);
            $comment->setArticle($article->incCommentCount());
            return $comment;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            ArticleFixtures::class,
        ];
    }
}
