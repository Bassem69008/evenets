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
        for ($sbj = 1; $sbj <= 50; ++$sbj) {
            $subject = new Subject();
            $subject->setTitle($faker->text(55));
            $subject->setSlug($this->slugger->slug($subject->getTitle())->lower());
            $subject->setDescription($faker->paragraph);
            $subject->setType($faker->randomElement(['Conférence', 'Atelier']));
            $subject->setIsPresented($faker->randomElement([false, true]));
            $subject->setDuration($faker->randomElement(['Court: 15min', 'Moyen: 30min', 'Long: 45min']));
            $subject->setStatus($faker->randomElement(['draft', 'reviewed', 'published', 'rejected']));
            $user = $this->getReference('usr-'.\rand(1, 15));
            $subject->setOwnerId($user);
            $subject->setSpeacker($user);
            $this->setReference('sbj-'.$sbj, $subject);

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
