<?php

namespace App\services;

use App\Entity\AnnonceCategory;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    public function getAllCategories(): array
    {
        return $this->em->getRepository(AnnonceCategory::class)->findBy([], ['name' => 'ASC']);
    }
}