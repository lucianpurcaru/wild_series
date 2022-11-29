<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        ['id' => 0,
        'title' => 'Mercredi',
        'synopsis' => 'Une série sur Wednesday Addams',
        'category' => 'category_Comédie'],
        ['id' => 1,
        'title' => 'The Crown',
        'synopsis' => 'Une série sur la Reine Elisabeth',
        'category' => 'category_Drame'],
        ['id' => 2,
        'title' => 'LOL: qui rit, sort!',
        'synopsis' => 'Une série sur des comédiens qui n\'ont pas le droit de rire',
        'category' => 'category_Comédie'],
        ['id' => 3,
        'title' => 'The Last Of Us',
        'synopsis' => 'Une série inspirée du jeu vidéo',
        'category' => 'category_Aventure'],
        ['id' => 4,
        'title' => 'The Crown',
        'synopsis' => 'Une série sur la Reine Elisabeth',
        'category' => 'category_Drame'],
    ];
    public function load(ObjectManager $manager)
    {

        foreach(self::PROGRAMS as $programItems) {
            $program = new Program();
            $program->setTitle($programItems['title']);
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
