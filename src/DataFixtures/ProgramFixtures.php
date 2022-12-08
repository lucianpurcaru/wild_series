<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const COUNTRIES = [
        'France',
        'Germany',
        'United Kingdom',
        'United States',
        'Japan',
        'Russia',
    ];
    const NB_PROGRAMS = 5;

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {

        foreach (CategoryFixtures::CATEGORIES as $key => $categoryName) {
            for ($i = 1; $i <= self::NB_PROGRAMS; $i++) {
                $program = new Program();
                $program->setTitle('Série ' . $key . $i);
                $program->setSynopsis('Une série populaire pour les amateurs du genre ' . $categoryName);
                $program->setCategory($this->getReference('category_' . $categoryName));
                $program->setCountry($this::COUNTRIES[$i]);
                $program->setYear(2000 + $i);
                $this->addReference('program_' . ($i + ($key * SELF::NB_PROGRAMS)), $program);
                $program->setSlug($this->slugger->slug($program->getTitle()));
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