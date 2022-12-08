<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    const NB_SEASONS = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= (count(CategoryFixtures::CATEGORIES) * ProgramFixtures::NB_PROGRAMS); $i++) {
            for ($j = 1; $j <= self::NB_SEASONS; $j++) {
                $season = new Season();
                $season->setNumber($j);
                $season->setYear($faker->year());
                $season->setDescription($faker->paragraphs(3, true));
                $season->setProgram($this->getReference('program_' . $i));
                $this->addReference($i . '_season_' . $j, $season);
                $manager->persist($season);
            }
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