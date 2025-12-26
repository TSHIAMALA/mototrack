<?php

namespace App\Repository;

use App\Entity\PcodeCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PcodeCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PcodeCategory::class);
    }

    public function findByLevel(int $level): array
    {
        return $this->createQueryBuilder('pc')
            ->where('pc.level = :level')
            ->setParameter('level', $level)
            ->orderBy('pc.label', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
