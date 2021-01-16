<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends ApiController
{
    /**
     * @Route("/author/{id}", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function getAction(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $author = $entityManager->getRepository(Author::class)->find($request->get('id'));

        return $this->makeResponse($author, 'author');
    }
}