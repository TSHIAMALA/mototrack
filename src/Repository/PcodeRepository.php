<?php

namespace App\Repository;

use App\Entity\Pcode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PcodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pcode::class);
    }

    public function findActiveByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.pcodeCategory', 'pc')
            ->where('pc.id = :categoryId')
            ->andWhere('p.isActive = true')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('p.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveByParent(?string $parentPcode): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.isActive = true');

        if ($parentPcode) {
            $qb->andWhere('p.parent = :parent')
               ->setParameter('parent', $parentPcode);
        } else {
            $qb->andWhere('p.parent IS NULL');
        }

        return $qb->orderBy('p.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCommunes(): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.pcodeCategory', 'pc')
            ->where('pc.level = :level')
            ->andWhere('p.isActive = true')
            ->setParameter('level', 4) // Niveau commune
            ->orderBy('p.label', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
