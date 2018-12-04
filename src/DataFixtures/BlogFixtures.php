<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Common\Persistence\ObjectManager;

class BlogFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Blog::class, 10, function(Blog $blog, $index) use ($manager) {
            $blog->setTitle($this->faker->title);
            $blog->setContent($this->faker->realText(2000));
        });

        $manager->flush();
    }
}
