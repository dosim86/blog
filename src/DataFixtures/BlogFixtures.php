<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Common\Persistence\ObjectManager;

class BlogFixtures extends BaseFixture
{
    const BLOG_COUNT = 100;

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Blog::class, self::BLOG_COUNT, function(Blog $blog, $index) use ($manager) {
            $blog->setTitle($this->faker->sentence);
            $blog->setContent($this->faker->realText(2000));
        });

        $manager->flush();
    }
}
