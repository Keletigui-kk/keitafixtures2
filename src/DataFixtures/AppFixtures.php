<?php

namespace App\DataFixtures;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        for($i = 0; $i<10; $i++){
            # ici on utilie une libraire qui s'appelle fake php pour remplir nos données
            $post = new Post();
            $post->setTitle($faker->sentence($nbWords = 2, $variableNbWords = true))
                ->setImage($faker->image())
                ->setContent($faker->sentence($nbWords = 10, $variableNbWords = true))
                ->setAuthor($faker->name())
                ->setCreatedAt($faker->dateTimeBetween('-6 months'));
            # on fait une persistance
            $manager->persist($post);
        }

        $manager->flush();
    }
}
