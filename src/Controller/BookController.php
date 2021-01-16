<?php

namespace App\Controller;

use App\Entity\Book;
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
        $book = $entityManager->getRepository(Book::class)->find($request->get('id'));

        return $this->makeResponse($book, 'book');
    }
}
