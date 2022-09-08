<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $filesystem = new Filesystem();
        $publicDir = __DIR__.'/../../public';
        $filesystem->remove($publicDir.'/users');
        $filesystem->mkdir($publicDir.'/users');

        $user = new User();
        $user->setEmail('fiorella@boxydev.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setFirstname('Fiorella');
        $user->setUsername('fiorella');
        $filesystem->copy('https://randomuser.me/api/portraits/women/'.rand(1, 99).'.jpg', $publicDir.'/users/fiorella.jpg');
        $user->setAvatar('users/fiorella.jpg');
        $user->setBirthday(new \DateTime('2019-12-31'));
        $user->setBiography('Une petite fille incroyable.');
        $manager->persist($user);

        for ($i = 0; $i <= 9; $i++) {
            $gender = $faker->randomElement(['men', 'women']);
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setFirstname($faker->firstName($gender === 'men' ? 'male' : 'female'));
            $user->setUsername($faker->userName());
            $filesystem->copy('https://randomuser.me/api/portraits/'.$gender.'/'.rand(1, 99).'.jpg', $publicDir.'/users/'.$user->getUsername().'.jpg');
            $user->setAvatar('users/'.$user->getUsername().'.jpg');
            $user->setBirthday($faker->dateTime());
            $user->setBiography($faker->text());
            $manager->persist($user);
            $this->addReference('user-'.$i, $user);
        }

        for ($i = 0; $i <= 50; $i++) {
            $post = new Post();
            $post->setContent($faker->text());
            $post->setCreator($this->getReference('user-'.rand(0, 9)));
            $manager->persist($post);
        }

        $manager->flush();
    }
}
