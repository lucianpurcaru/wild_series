<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $j = 1;
        foreach (CategoryFixtures::CATEGORIES as $category) {
            for ($i = 1; $i <= 5; $i++) {
                $program = new Program();
                $program->setTitle('Titre n°' . $j);
                $program->setPoster('Poster du titre n°' . $j);
                $program->setSynopsis('Synopsis du titre n°' . $j);
                $program->setCategory($this->getReference('category_' . $category));
                $j++;

                $manager->persist($program);
                $manager->flush();
            }
        }
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
