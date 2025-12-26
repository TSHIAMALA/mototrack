<?php

namespace App\Repository;

use App\Entity\Taxe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Taxe>
 */
class TaxeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxe::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCode(string $code): ?Taxe
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTotalMontant(): float
    {
        $result = $this->createQueryBuilder('t')
            ->select('SUM(t.montant) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    public function getAllCodes(): array
    {
        return [
            Taxe::CODE_VIGNETTE,
            Taxe::CODE_PLAQUE,
            Taxe::CODE_TCR,
        ];
    }
}
