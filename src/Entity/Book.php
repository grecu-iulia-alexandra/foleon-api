<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
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
    private string $title;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"author", "book"})
     */
    private int $publishingYear;

    /**
     * @ORM\ManyToMany(targetEntity="Author",mappedBy="books", cascade={"persist"})
     * @Groups({"book"})
     */
    private Collection $authors;

    /**
     * Book constructor.
     * @param string $title
     * @param int $publishingYear
     * @param Collection $authors
     */
    public function __construct(
        string $title,
        int $publishingYear,
        Collection $authors
    ) {
        $this->title = $title;
        $this->publishingYear = $publishingYear;
        $this->authors = new ArrayCollection([]);

        foreach ($authors as $author) {
            $author->addBook($this);
        }
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getPublishingYear(): int
    {
        return $this->publishingYear;
    }

    /**
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }
}