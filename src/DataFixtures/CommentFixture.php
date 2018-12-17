<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixture extends BaseFixture implements DependentFixtureInterface
{
    const REF_NAME = 'comment';

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(90, self::REF_NAME, function ($index) use ($manager) {
            $comment = new Comment();
            $comment->setText($this->faker->words(10, true));
            $comment->setCreatedAt($this->faker->dateTimeBetween('-30 days'));
            $comment->setOwner($this->getRandomReference(UserFixture::REF_NAME));
            $comment->setArticle($this->getRandomReference(ArticleFixtures::REF_NAME));
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
