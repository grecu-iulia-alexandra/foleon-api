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

        if ($author) {
            return $this->makeResponse($author, 'author');
        }

        return $this->makeResponse($author, 'author', 404);
    }

    /**
     * @Route("/author", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function postAction(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $author = json_decode($request->getContent(), true);

        if (! (isset($author['firstName']) && isset($author['lastName']))) {
            return $this->makeResponse(null, 'author', 422);
        }

        $authorEntity = new Author (
            $author['firstName'],
            $author['lastName']
        );

        $entityManager->persist($authorEntity);
        $entityManager->flush();

        return $this->makeResponse($authorEntity, 'author', 201);
    }
}