<?php

namespace App\Tests\Controller;

use App\DataFixtures\AuthorFixtures;
use App\DataFixtures\BookFixtures;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AuthorControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $authorFixtures = new AuthorFixtures();
        $authorFixtures->load($this->entityManager);

        /** @var AuthorRepository $authorRepository */
        $authorRepository = $this->entityManager->getRepository(Author::class);
        $bookFixtures = new BookFixtures($authorRepository);
        $bookFixtures->load($this->entityManager);
    }

    public function testShowBook()
    {
        $author = $this->entityManager->getRepository(Author::class)->findOneBy([]);

        $this->client->request('GET', '/author/' . $author->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testShowBookDoesNotExist()
    {
        $authorId = $this->entityManager->getRepository(Author::class)->count([]) + 1;

        $this->client->request('GET', '/author/' . $authorId);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
