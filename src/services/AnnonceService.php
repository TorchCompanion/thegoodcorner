<?php

namespace App\services;

use App\Entity\Annonce;
use App\Entity\AnnoncePicture;
use Doctrine\ORM\EntityManagerInterface;

class AnnonceService
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    /**
     * @param array $filters
     * @param array $order
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAnnonces(
        array $filters,
        array $order,
        int   $page = 1,
        int   $limit = 10
    ): array
    {
        if ($page < 1) {
            $page = 1;
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('a')
            ->from(Annonce::class, 'a')
            ->where('1 = 1');

        if (isset($filters['query'])) {
            $qb->andWhere('a.title LIKE :query')
                ->orWhere('a.description LIKE :query')
                ->setParameter('query', '%' . $filters['query'] . '%');
        }

        if (isset($filters['in_categories'])) {
            $qb->andWhere('a.category IN (:categories)')
                ->setParameter('categories', $filters['in_categories']);
        }

        if (isset($filters['price_sup'])) {
            $qb->andWhere('a.price >= :pricesup')
                ->setParameter('pricesup', $filters['price_sup']);
        }

        if (isset($filters['price_inf'])) {
            $qb->andWhere('a.price <= :priceinf')
                ->setParameter('priceinf', $filters['price_inf']);
        }

        if (isset($filters['hasPicture'])) {
//            $qb->leftjoin(AnnoncePicture::class, 'p')
//                ->andWhere('a.pictures IS NOT EMPTY')
//                ->setParameter('hasPicture', $filters['hasPicture']);
            $qb->andWhere('a.Pictures IS NOT EMPTY');
        }

        if (isset($filters['owner'])) {
            $qb->andWhere('a.owner = :owner')
                ->setParameter('owner', $filters['owner']);
        }

        if (isset($order['title'])) {
            $qb->orderBy('a.title', $order['title']);
        }
        if (isset($order['price'])) {
            $qb->orderBy('a.price', $order['price']);
        }

        if (isset($order['rating'])) {
            $qb->orderBy('a.rating', $order['rating']);
        }

        if (isset($order['postedDate'])) {
            $qb->orderBy('a.postedDate', $order['postedDate']);
        }

        $qbCount = clone $qb;

        $qb->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);
        // count all result
        $qbCount->select('count(a.id)');
        $total = (int)$qbCount->getQuery()->getSingleScalarResult();
        $totalPages = (int)ceil($total / $limit);

        if ($total === 0) {
            throw new \RuntimeException('No results !', 0);
        }

        if ($page > $totalPages) {
            throw new \RuntimeException('This page does not exist !', 10);
        }

        $res = $qb->getQuery()->getResult();
        return [
            'results' => $res,
            'count' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }
}