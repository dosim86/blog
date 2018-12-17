<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\BookmarkArticle;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BookmarkArticleFixture extends BaseFixture implements DependentFixtureInterface
{
    const REF_NAME = 'bookmark_article';

    private $dublicate = [];

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(90, self::REF_NAME, function ($index) {
            while (true) {
                $user = $this->getRandomReference(UserFixture::REF_NAME);
                $article = $this->getRandomReference(ArticleFixtures::REF_NAME);
                $combineId = $user->getId().'_'.$article->getId();
                if (!in_array($combineId, $this->dublicate, true)) {
                    $this->dublicate[] = $combineId;
                    break;
                }
            }
            $bookmark = new BookmarkArticle();
            $bookmark->setUser($user);
            $bookmark->setArticle($article);
            return $bookmark;
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
