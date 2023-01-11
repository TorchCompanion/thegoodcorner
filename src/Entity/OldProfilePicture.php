<?php

namespace App\Entity;

use App\Repository\OldProfilePictureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OldProfilePictureRepository::class)]
class OldProfilePicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'oldProfilePictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProfilePicture $Picture = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPicture(): ?ProfilePicture
    {
        return $this->Picture;
    }

    public function setPicture(?ProfilePicture $Picture): self
    {
        $this->Picture = $Picture;

        return $this;
    }
}
