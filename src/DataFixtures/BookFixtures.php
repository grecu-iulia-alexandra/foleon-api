<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    private AuthorRepository $authorRepository;

    /**
     * BookFixtures constructor.
     * @param AuthorRepository $authorRepository
     */
    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function load(ObjectManager $manager)
    {
        $generator = \Faker\Factory::create();

        $authors = $this->authorRepository->findAll();

        for ($i=0; $i <= 50; $i++) {
            $book = new Book(
                $generator->sentence,
                $this->generatePublishingYear(),
                $this->pickAuthors($authors)
            );

            $manager->persist($book);
        }
        $manager->flush();
    }

    private function pickAuthors(array $authors) : Collection
    {
        $numberOfAuthors = rand(1, 4);

        $pickedAuthors = [];
        $i = 0;
        while ($i < $numberOfAuthors) {
            $pickedAuthor = $this->pickAuthor($authors);
            if(! in_array($pickedAuthor, $pickedAuthors)) {
                $pickedAuthors[] = $pickedAuthor;
            }

            $i++;
        }

        return new ArrayCollection($pickedAuthors);
    }

    private function pickAuthor(array $authors) :Author
    {
        $index = rand(0, count($authors) - 1);

        return $authors[$index];
    }

    private function generatePublishingYear() : int
    {
        return rand(1900, 2010);
    }

    public function getDependencies()
    {
        return [
            AuthorFixtures::class,
        ];
    }
}
