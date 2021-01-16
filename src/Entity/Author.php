<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AuthorRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"author", "book"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"author", "book"})
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"author", "book"})
     */
    private string $lastName;


    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="authors", cascade={"persist"})
     * @Groups({"author"})
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
        $this->books = new ArrayCollection([]);
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
        $this->books->add($book);

        return $this;
    }
}