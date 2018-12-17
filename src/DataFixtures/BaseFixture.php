<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    /** @var Generator */
    protected $faker;

    private $referencesIndex = [];

    abstract protected function loadData(ObjectManager $manager);

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->loadData($manager);
    }

    protected function createMany(int $count, string $refName, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $factory($i);
            $this->manager->persist($entity);
            $this->addReference($refName.'_'.$i, $entity);
        }
    }

    protected function getRandomReference(string $refName)
    {
        if (!isset($this->referencesIndex[$refName])) {
            $this->referencesIndex[$refName] = [];
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (strpos($key, $refName.'_') === 0) {
                    $this->referencesIndex[$refName][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$refName])) {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $refName));
        }
        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$refName]);
        return $this->getReference($randomReferenceKey);
    }
}
