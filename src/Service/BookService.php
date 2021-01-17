<?php

namespace App\Service;

use App\Entity\Author;
use App\Exception\InvalidDataException;
use App\Exception\NotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function makeAuthorsCollection(array $authors): Collection
    {
        $result = [];
        $authorRepository = $this->entityManager->getRepository(Author::class);
        foreach ($authors as $author) {
            $authorEntity = null;
            if (is_numeric($author)){
                $authorEntity = $authorRepository->find($author);
                if ($authorEntity === null) {
                    throw new NotFoundException(
                        sprintf(
                            "Invalid author provided with ID: %d",
                            $author
                        )
                    );
                }

                $result[] = $authorEntity;
            }

            if (is_array($author)) {
                if (! (isset($author['firstName']) && isset($author['lastName']))) {
                    throw new InvalidDataException(
                        'Invalid data for author. Please provide a first name and last name'
                    );
                }
                $authorEntity = new Author(
                    $author['firstName'],
                    $author['lastName']
                );

                $result[] = $authorEntity;
            }

            if ($authorEntity) {
                $this->entityManager->persist($authorEntity);
            }
        }

        return new ArrayCollection($result);
    }
}
