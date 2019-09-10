<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use DavidBadura\FakerMarkdownGenerator\FakerProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

    /**
     * @var UserPasswordEncoderInterface
     */
     protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager) {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new FakerProvider($faker));

        for($u = 0; $u <10; $u++) {
            $user = new User();
            $user->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail("user$u@gmail.com");

            $password = $this->encoder->encodePassword($user, "pass");

            $user->setPassword($password);

            $manager->persist($user);
        }

        $categories = [];

        for ($b = 0; $b < 10; $b++) {
            $category = new Category();

            $category->setTitle($faker->realText(10))
                ->setSlug($faker->slug)
                ->setDescription($faker->markdownP());

            $manager->persist($category);

            $categories[] = $category;
        }

        for ($i = 0; $i < 40; $i++) {
            $article = new Article();

            $article->setTitle($faker->catchPhrase())
                    ->setSlug($faker->slug())
                    ->setImage('https://placehold.it/400x200')
                    ->setContent($faker->markdown())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'));

            $randomCategories = $faker->randomElements($categories, mt_rand(1, 3));

            foreach ($randomCategories as $category) {
                $article->addCategory($category);
            }

            if ($faker->boolean()) {
                $article->setPublishedAt($faker->dateTimeBetween('-6 months'));

                for ($a = 0; $a < mt_rand(0, 10); $a++) {
                    $comment = new Comment();

                    $comment->setAuthorName($faker->firstName)
                            ->setContent($faker->realText(180))
                            ->setCreatedAt($faker->dateTimeBetween('-5 months'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }

            $manager->persist($article);
        }

        $manager->flush();
    }
}
