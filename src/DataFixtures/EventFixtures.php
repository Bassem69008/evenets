<?php

namespace App\DataFixtures;

use App\Entity\Events;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $now = new \DateTime();
        for ($i = 1; $i <= 10; ++$i) {
            $event = new Events();
            $event->setTitle($faker->text(55));
            $event->setSlug($this->slugger->slug($event->getTitle())->lower());
            $event->setDescription($faker->paragraph);
            $event->setDate($faker->dateTimeBetween('-2 months', '+6 months'));
            $event->getDate() > $now ? $event->setStatus('In progress') : $event->setStatus('Done');
            $event->setCreatedBy($this->getReference('usr-'.\rand(1, 15)));
            for ($j = 1; $j <= 5; ++$j) {
                $subject = $this->getReference('sbj-'.\rand(1, 50));
                if (null == $subject->getSpeacker()) {
                    $subject->setSpeacker($this->getReference('usr-'.\rand(1, 15)));
                    $manager->persist($subject);
                }
            }
            // add reference
            $this->setReference('evt-'.$i, $event);
            $manager->persist($event);
        }
        $manager->flush();
    }

    /**
     *  method to force to load SubjectFixtures Before EventFixtures.
     */
    public function getDependencies(): array
    {
        return [
            SubjectFixtures::class,
        ];
    }
}
