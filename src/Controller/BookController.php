<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Exception\FoleonApiException;
use App\Exception\InvalidDataException;
use App\Exception\NotFoundException;
use App\Service\BookService;
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
     * @param BookService $bookService
     * @return JsonResponse
     */
    public function postAction(
        Request $request,
        EntityManagerInterface $entityManager,
        BookService $bookService
    ) {
        $content = json_decode($request->getContent(), true);

        if (! (isset($content['authors']) && isset($content['title']) && isset($content['publishingYear']))) {
            return $this->makeResponse(null, 'book', 422);
        }

        try {
            $authors = $bookService->makeAuthorsCollection($content['authors']);
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
}
