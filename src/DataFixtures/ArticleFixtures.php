<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixture implements DependentFixtureInterface
{
    const REF_NAME = 'article';

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(30, self::REF_NAME, function ($index) use ($manager) {
            $article = new Article();
            $article->setTitle($this->faker->sentence);
            $article->setContent($this->faker->realText(2000));
            $article->setAuthor($this->getRandomReference(UserFixture::REF_NAME));
            $article->addTag($this->getRandomReference(TagFixture::REF_NAME));
            $article->addTag($this->getRandomReference(TagFixture::REF_NAME));
            $article->addTag($this->getRandomReference(TagFixture::REF_NAME));
            $article->addTag($this->getRandomReference(TagFixture::REF_NAME));
            $article->addTag($this->getRandomReference(TagFixture::REF_NAME));
            return $article;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            TagFixture::class,
        ];
    }
}
