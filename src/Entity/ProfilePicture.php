<?php

namespace App\Entity;

use App\Repository\ProfilePictureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilePictureRepository::class)]
class ProfilePicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $absolutePath = null;

    #[ORM\Column(length: 255)]
    private ?string $webPath = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbsolutePath(): ?string
    {
        return $this->absolutePath;
    }

    public function setAbsolutePath(string $absolutePath): self
    {
        $this->absolutePath = $absolutePath;

        return $this;
    }

    public function getWebPath(): ?string
    {
        return $this->webPath;
    }

    public function setWebPath(string $webPath): self
    {
        $this->webPath = $webPath;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
