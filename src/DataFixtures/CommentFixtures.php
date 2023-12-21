<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\SubjectRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private $counter = 1;

    public function __construct(private SubjectRepository $subjectRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 1; $i <= 50; ++$i) {
            $parent = $this->createComment(
                $faker->paragraph,
                null,
                $this->getReference('usr-'.\rand(1, 15)),
                $manager

            );


            for ($j = 1; $j <= 10; ++$j) {
                $this->createComment(
                    $faker->paragraph,
                    $parent,
                    $this->getReference('usr-'.\rand(1, 15)),
                    $manager
                );

            }
        }

        $manager->flush();
    }

    public function createComment(string $content, Comment $parent = null, User $user,ObjectManager $manager): Comment
    {
        $faker = Factory::create('fr_FR');
        $comment = (new Comment())
        ->setContent($content)
        ->setParent($parent)
        ->setUser($user)
        ->setType($faker->randomElement(['subject','event']))
        ->setIsActive(true);
   // si c'est un parent
        if($parent == null)
        {
            $comment->getType() == 'subject' ?
            $comment->setSubjects($this->getReference('sbj-'.\rand(1, 15))):
            $comment->setEvent($this->getReference('evt-'.\rand(1, 15)));
        }
        // si c'est un child
        else{
           $comment->setType($parent->getType());
        }

        $manager->persist($comment);

        return $comment;
    }

    /**
     *  method to force to load SubjectFixtures. BeforeCommentFixtures.
     */
    public function getDependencies(): array
    {
        return [
            SubjectFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['comment'];
    }
}
