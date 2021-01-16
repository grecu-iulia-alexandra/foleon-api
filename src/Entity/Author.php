<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AuthorRepository;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName;


    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="authors", cascade={"persist"})
     */
    private Collection $books;

    /**
     * Author constructor.
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(
        string $firstName,
        string $lastName
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Collection $books
     * @return $this
     */
    public function setBooks(Collection $books): Author
    {
        $this->books = $books;

        return $this;
    }

    public function addBook(Book $book)
    {
        $this->books[] = $book;

        return $this;
    }
}