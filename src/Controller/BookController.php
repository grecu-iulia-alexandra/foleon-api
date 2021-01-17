<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Exception\FoleonApiException;
use App\Exception\InvalidDataException;
use App\Exception\NotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends ApiController
{
    /**
     * @Route("/book/{id}", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function getAction(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $cacheKey = str_replace('/', '-', $request->getRequestUri());
        $bookCache = $this->getCache()->getItem($cacheKey);
        if ($bookCache->isHit()) {
            return $this->makeResponse($bookCache->get(), 'book');
        }

        $book = $entityManager->getRepository(Book::class)->find($request->get('id'));
        $bookCache->set($this->getSerializer()->serialize($book, 'json',  ['groups' => 'book']));
        $this->getCache()->save($bookCache);

        if ($book) {
            return $this->makeResponse($book, 'book');
        }

        return $this->makeResponse($book, 'book', 404);
    }

    /**
     * @Route("/book", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function postAction(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $content = json_decode($request->getContent(), true);

        if (! (isset($content['authors']) && isset($content['title']) && isset($content['publishingYear']))) {
            return $this->makeResponse(null, 'book', 422);
        }

        try {
            $authors = $this->getAuthors($content['authors'], $entityManager);
        } catch (FoleonApiException $exception) {
            return $this->makeResponse($exception->getMessage(), '', 422);
        }

        $book = new Book (
            $content['title'],
            $content['publishingYear'],
            $authors
        );

        $entityManager->persist($book);
        $entityManager->flush();

        $entityManager->refresh($book);

        return $this->makeResponse($book, 'book', 201);
    }

    private function getAuthors(array $authors, EntityManagerInterface $entityManager): Collection
    {
        $result = [];
        $authorRepository = $entityManager->getRepository(Author::class);
        foreach ($authors as $author) {
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

            if (! (isset($author['firstName']) && isset($author['lastName']))) {
                throw new InvalidDataException(
                    'Invalid data for author. Please provide a first name and last name'
                );
            }
            $authorEntity = new Author(
                $author['firstName'],
                $author['lastName']
            );

            $entityManager->persist($authorEntity);

            $result[] = $authorEntity;
        }

        return new ArrayCollection($result);
    }
}
