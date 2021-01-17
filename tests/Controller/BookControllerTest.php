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


class BookControllerTest extends WebTestCase
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
        $book = $this->entityManager->getRepository(Book::class)->findOneBy([]);

        $this->client->request('GET', '/book/' . $book->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testShowBookDoesNotExist()
    {
        $bookId = $this->entityManager->getRepository(Book::class)->count([]) + 1;

        $this->client->request('GET', '/book/' . $bookId);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testPostBook()
    {
        $this->client->request(
            'POST',
            '/book',
            [],
            [],
            [],
            json_encode([
                'title' => 'Book title test',
                'publishingYear' => 1990,
                'authors' => [
                    [
                        'firstName' => 'John',
                        'lastName' => 'Smith'
                    ]
                ]
            ])
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function testPostBookInvalidData() {
        $this->client->request(
            'POST',
            '/book'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }
}
