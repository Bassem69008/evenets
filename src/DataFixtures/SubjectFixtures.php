<?php

namespace App\DataFixtures;

use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class SubjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($sbj = 1; $sbj <= 15; ++$sbj) {
            $subject = new Subject();
            $subject->setTitle($faker->title);
            $subject->setSlug($this->slugger->slug($subject->getTitle())->lower());
            $subject->setDescription($faker->text);
            $subject->setType($faker->randomElement(['Conférence', 'Atelier']));
            $subject->setDuration($faker->randomElement(['Court: 15min', 'Moyen: 30min', 'Long: 45min']));
            $subject->setStatus($faker->randomElement(['draft', 'viewed', 'published', 'rejected']));
            $owner = $this->getReference('usr-'.\rand(1, 15));
            $subject->setOwnerId($owner);

            $manager->persist($subject);
        }

        $manager->flush();
    }

    /**
     *  method to force to load userFixtures Before SubjectFixtures.
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}