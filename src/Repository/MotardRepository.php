<?php

namespace App\Repository;

use App\Entity\Motard;
use App\Entity\Site;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Motard>
 */
class MotardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motard::class);
    }

    public function findByCreator(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySite(Site $site): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.createdBy', 'u')
            ->andWhere('u.site = :site')
            ->setParameter('site', $site)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $query, ?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.nomComplet LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        if ($site) {
            $qb->join('m.createdBy', 'u')
               ->andWhere('u.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->orderBy('m.nomComplet', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function countByType(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.type, COUNT(m.id) as total')
            ->groupBy('m.type')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiques(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id) as total')
            ->addSelect("SUM(CASE WHEN m.type = 'PHYSIQUE' THEN 1 ELSE 0 END) as physiques")
            ->addSelect("SUM(CASE WHEN m.type = 'MORALE' THEN 1 ELSE 0 END) as morales");

        if ($site) {
            $qb->join('m.createdBy', 'u')
               ->andWhere('u.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getSingleResult();
    }
}
