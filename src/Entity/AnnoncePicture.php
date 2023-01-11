<?php

namespace App\Entity;

use App\Repository\AnnoncePictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnoncePictureRepository::class)]
class AnnoncePicture
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

    #[ORM\ManyToOne(inversedBy: 'Pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Annonce $annonce = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
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
