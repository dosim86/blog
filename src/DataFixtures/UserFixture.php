<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'user', function($index) use ($manager) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setFirstname($this->faker->firstName);
            $user->setPassword($this->encoder->encodePassword($user, '123'));
            return $user;
        });

        $manager->flush();
    }
}
