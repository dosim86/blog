<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixture extends BaseFixture
{
    const REF_NAME = 'tag';

    private $dublicate = [];

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(200, self::REF_NAME, function ($index) use ($manager) {
            $tag = new Tag();
            $tag->setName($this->faker->unique()->words(2, true));
            return $tag;
        });

        $manager->flush();
    }
}
