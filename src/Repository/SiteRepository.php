<?php

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Site>
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('s.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countMotosPerSite(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id, s.nom, COUNT(m.id) as totalMotos')
            ->leftJoin('s.motos', 'm')
            ->groupBy('s.id')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiques(): array
    {
        $qb = $this->createQueryBuilder('s');
        
        return $qb->select('s.id, s.nom, s.localisation')
            ->addSelect('(SELECT COUNT(m.id) FROM App\Entity\Moto m WHERE m.site = s) as totalMotos')
            ->addSelect('(SELECT COUNT(u.id) FROM App\Entity\User u WHERE u.site = s) as totalUsers')
            ->where('s.isActive = true')
            ->getQuery()
            ->getResult();
    }
}
