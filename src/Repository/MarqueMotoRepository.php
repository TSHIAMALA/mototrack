<?php

namespace App\Repository;

use App\Entity\MarqueMoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarqueMoto>
 */
class MarqueMotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarqueMoto::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countMotosPerMarque(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.id, m.nom, COUNT(mo.id) as totalMotos')
            ->leftJoin('m.motos', 'mo')
            ->groupBy('m.id')
            ->orderBy('totalMotos', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
