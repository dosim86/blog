<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixture extends BaseFixture
{
    const REF_NAME = 'category';

    private static $categories = [
        'Medicine',
        'History',
        'Society',
        'Culture',
        'Science',
        'Politic',
        'Cooking',
        'Music',
        'Sport',
        'IT',
    ];

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(count(self::$categories), self::REF_NAME, function ($index) use ($manager) {
            $comment = new Category();
            $comment->setName(self::$categories[$this->faker->unique()->numberBetween(0, 9)]);
            return $comment;
        });

        $manager->flush();
    }
}
