<?php

namespace App\Repository;

use App\Entity\Moto;
use App\Entity\Site;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Moto>
 */
class MotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moto::class);
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
            ->andWhere('m.site = :site')
            ->setParameter('site', $site)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithoutPlaque(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.affectationPlaque', 'ap')
            ->andWhere('ap.id IS NULL');

        if ($site) {
            $qb->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithPlaque(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->join('m.affectationPlaque', 'ap');

        if ($site) {
            $qb->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEligibleForPlaque(Site $site): array
    {
        // Motos sans plaque avec tous les paiements validés
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "SELECT m.* FROM moto m
                LEFT JOIN affectation_plaque ap ON ap.moto_id = m.id
                WHERE ap.id IS NULL
                AND m.site_id = :site_id
                AND (
                    SELECT COUNT(DISTINCT p.taxe_id) 
                    FROM paiement p 
                    WHERE p.moto_id = m.id AND p.status = 'VALIDE'
                ) = 3
                ORDER BY m.created_at DESC";
        
        $stmt = $conn->executeQuery($sql, ['site_id' => $site->getId()]);
        $results = $stmt->fetchAllAssociative();
        
        $motos = [];
        foreach ($results as $row) {
            $motos[] = $this->find($row['id']);
        }
        
        return $motos;
    }

    public function getStatistiques(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id) as total')
            ->addSelect('SUM(CASE WHEN ap.id IS NOT NULL THEN 1 ELSE 0 END) as avecPlaque')
            ->addSelect('SUM(CASE WHEN ap.id IS NULL THEN 1 ELSE 0 END) as sansPlaque')
            ->leftJoin('m.affectationPlaque', 'ap');

        if ($site) {
            $qb->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getSingleResult();
    }

    public function countBySite(): array
    {
        return $this->createQueryBuilder('m')
            ->select('s.id, s.nom, COUNT(m.id) as total')
            ->join('m.site', 's')
            ->groupBy('s.id')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiquesBySite(): array
    {
        return $this->createQueryBuilder('m')
            ->select('s.nom, COUNT(m.id) as total')
            ->join('m.site', 's')
            ->groupBy('s.id')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
