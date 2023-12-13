<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {


        for($i= 1; $i<=20; $i++)
        {
            $subscription = new Subscription();
            $user = $this->getReference('usr-'.\rand(1, 15));
            $event = $this->getReference('evt-'.\rand(1, 10));
            $subscription->setUser($user)
            ->setEvent($event);
            $manager->persist($subscription);

        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class,
        ];
    }
}
