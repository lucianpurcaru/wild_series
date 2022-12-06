<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $programs = count(CategoryFixtures::CATEGORIES) * ProgramFixtures::NB_PROGRAMS;

        for ($i = 1; $i <= 10; $i++) {
                $actor = new Actor();
                $actor->setName($faker->name());
                $actor->addProgram($this->getReference('program_' . $faker->numberBetween(1, $programs)));
                $actor->addProgram($this->getReference('program_' . $faker->numberBetween(1, $programs)));
                $actor->addProgram($this->getReference('program_' . $faker->numberBetween(1, $programs)));
                $manager->persist($actor);
            }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}