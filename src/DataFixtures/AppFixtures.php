<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($a = 0; $a < 40; $a++) {
            $article = new Article();
            $article->setTitle($faker->catchPhrase)
                ->setSlug($faker->slug)
                ->setImage("http://placehold.it/400x200")
                ->setContent($faker->realText)
                ->setCreatedAt($faker->dateTimeBetween("-6 months"));

            $manager->persist($article);
        }
        $manager->flush();
    }
}
