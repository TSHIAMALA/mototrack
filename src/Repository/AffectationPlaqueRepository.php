<?php

namespace App\Repository;

use App\Entity\AffectationPlaque;
use App\Entity\Site;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AffectationPlaque>
 */
class AffectationPlaqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AffectationPlaque::class);
    }

    public function findBySite(Site $site): array
    {
        return $this->createQueryBuilder('ap')
            ->join('ap.moto', 'm')
            ->andWhere('m.site = :site')
            ->setParameter('site', $site)
            ->orderBy('ap.validatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByValidator(User $user): array
    {
        return $this->createQueryBuilder('ap')
            ->andWhere('ap.validatedBy = :user')
            ->setParameter('user', $user)
            ->orderBy('ap.validatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiques(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('ap')
            ->select('COUNT(ap.id) as total');

        if ($site) {
            $qb->join('ap.moto', 'm')
               ->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getSingleResult();
    }

    public function getRecentAffectations(?Site $site = null, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('ap')
            ->join('ap.moto', 'm')
            ->join('ap.plaque', 'p')
            ->orderBy('ap.validatedAt', 'DESC')
            ->setMaxResults($limit);

        if ($site) {
            $qb->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByMonth(?Site $site = null, int $months = 6): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $siteCondition = $site ? "AND m.site_id = :site_id" : "";
        
        $sql = "SELECT 
                    DATE_FORMAT(ap.validated_at, '%Y-%m') as mois,
                    COUNT(ap.id) as total
                FROM affectation_plaque ap
                JOIN moto m ON m.id = ap.moto_id
                WHERE ap.validated_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                {$siteCondition}
                GROUP BY mois
                ORDER BY mois DESC";
        
        $params = ['months' => $months];
        if ($site) {
            $params['site_id'] = $site->getId();
        }
        
        return $conn->executeQuery($sql, $params)->fetchAllAssociative();
    }
}
