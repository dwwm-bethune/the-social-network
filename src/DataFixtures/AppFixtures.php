<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setEmail('fiorella@boxydev.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setFirstname('Fiorella');
        $user->setUsername('fiorella');
        $user->setAvatar('users/fiorella.jpeg');
        $user->setBirthday(new \DateTime('2019-12-31'));
        $user->setBiography('Une petite fille incroyable.');
        $manager->persist($user);

        for ($i = 0; $i <= 10; $i++) {
            $post = new Post();
            $post->setContent($faker->text());
            $post->setCreator($user);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
