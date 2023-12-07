<?php

namespace App\DataFixtures;

use App\Entity\SubjectLike;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SubjectLikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < mt_rand(1, 100); ++$i) {
            $like = new SubjectLike();
            $user = $this->getReference('usr-'.\rand(1, 15));
            $subject = $this->getReference('sbj-'.\rand(1, 50));
            $like->setSubject($subject);
            $like->setUser($user);
            $manager->persist($like);
        }

        $manager->flush();
    }

    /**
     *  method to force to load SubjectFixtures. Before SubjectLikeFixtures.
     */
    public function getDependencies(): array
    {
        return [
            SubjectFixtures::class,
        ];
    }
}
