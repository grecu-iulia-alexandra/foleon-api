<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
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
    private string $title;

    /**
     * @ORM\Column(type="integer")
     */
    private int $publishingYear;

    /**
     * @ORM\ManyToMany(targetEntity="Author",mappedBy="books", cascade={"persist"})
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