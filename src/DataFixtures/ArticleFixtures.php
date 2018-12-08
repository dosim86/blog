<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(100, 'article', function($index) use ($manager) {
            $article = new Article();
            $article->setTitle($this->faker->sentence);
            $article->setContent($this->faker->realText(2000));
            $article->setAuthor($this->getRandomReference('user'));
            return $article;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
