<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    
    public const PROGRAMS = [
        ['id' => 0,
        'title' => 'Lucifer',
        'synopsis' => 'The Devil among us',
        'category' => 'category_Action'],
        ['id' => 1,
        'title' => 'Brooklyn Nine Nine',
        'synopsis' => 'Fun at the police station',
        'category' => 'category_Comedie'],
        ['id' => 2,
        'title' => 'Snowpiercer',
        'synopsis' => 'The last humans all in one train',
        'category' => 'category_Aventure'],
        ['id' => 3,
        'title' => 'Arcane',
        'synopsis' => 'Jinx and Vi will show you a rael serie',
        'category' => 'category_Aventure'],
        ['id' => 4,
        'title' => 'Vikings',
        'synopsis' => 'Ragnar and his capaigne',
        'category' => 'category_Action'],
    ];

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager): void
    {
        foreach(self::PROGRAMS as $programItems) {
            $program = new Program();
            $program->setTitle($programItems['title']);
            $program->setSlug($this->slugger->slug($program->getTitle()));
            $program->setSynopsis($programItems['synopsis']);
            $program->setCategory($this->getReference($programItems['category']));
            $manager->persist($program);
            $this->addReference('program_' . $programItems['id'], $program);
            
        }

        $manager->flush();
        
    }

    public function getDependencies()
    {
        return [
          CategoryFixtures::class,
        ];
    }

}