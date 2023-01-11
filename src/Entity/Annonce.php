<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $rating;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnnonceCategory $category = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $postedDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '2')]
    private ?string $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: AnnoncePicture::class)]
    private Collection $Pictures;

    public function __construct()
    {
        $this->rating = 0;
        $this->Pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCategory(): ?AnnonceCategory
    {
        return $this->category;
    }

    public function setCategory(?AnnonceCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPostedDate(): ?\DateTimeInterface
    {
        return $this->postedDate;
    }

    public function setPostedDate(\DateTimeInterface $postedDate): self
    {
        $this->postedDate = $postedDate;

        return $this;
    }


    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, AnnoncePicture>
     */
    public function getPictures(): Collection
    {
        return $this->Pictures;
    }

    public function addPicture(AnnoncePicture $picture): self
    {
        if (!$this->Pictures->contains($picture)) {
            $this->Pictures->add($picture);
            $picture->setAnnonce($this);
        }

        return $this;
    }

    public function removePicture(AnnoncePicture $picture): self
    {
        if ($this->Pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAnnonce() === $this) {
                $picture->setAnnonce(null);
            }
        }

        return $this;
    }
}
