<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $generator = \Faker\Factory::create();

        for ($i = 0; $i <= 20 ; $i++) {
            $author = new Author(
                $generator->firstName,
                $generator->lastName
            );

            $manager->persist($author);
        }

        $manager->flush();
    }
}
