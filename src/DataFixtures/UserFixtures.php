<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $contributor = new User();
        $contributor->setEmail('anon@hotmail.com');
        $contributor->setUsername('AnonUser');
        $contributor->setRoles(['ROLE_CONTRIBUTOR']);
        $hashedPassword = $this->passwordHasher->hashPassword($contributor, 'userpassword');
        $contributor->setPassword($hashedPassword);
        $this->addReference('user_contributor', $contributor);
        $manager->persist($contributor);

        $admin = new User();
        $admin->setEmail('admin@wildseries.com');
        $admin->setUsername('Lord_BanHammer');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'adminpassword');
        $admin->setPassword($hashedPassword);
        $this->addReference('user_admin', $admin);
        $manager->persist($admin);

        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@wildseries.com');
            $user->setUsername('User' . $i);
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);
            $this->addReference('user_user' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}