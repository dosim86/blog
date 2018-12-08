<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{
    const MAIN_USER_COUNT = 10;

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
        $this->createMany(User::class, self::MAIN_USER_COUNT, function(User $user, $index) use ($manager) {
            $user->setEmail($this->faker->email);
            $user->setFirstname($this->faker->firstName);
            $user->setPassword($this->encoder->encodePassword($user, '123'));
        });

        $manager->flush();
    }
}
