<?php

namespace App\Repository;

use App\Entity\Moto;
use App\Entity\Paiement;
use App\Entity\Site;
use App\Entity\Taxe;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function findByCreator(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySite(Site $site): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.moto', 'm')
            ->andWhere('m.site = :site')
            ->setParameter('site', $site)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEnAttenteBySite(Site $site): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.moto', 'm')
            ->andWhere('m.site = :site')
            ->andWhere('p.status = :status')
            ->setParameter('site', $site)
            ->setParameter('status', Paiement::STATUS_EN_ATTENTE)
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByMoto(Moto $moto): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.moto = :moto')
            ->setParameter('moto', $moto)
            ->orderBy('p.taxe', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findValidatedByMoto(Moto $moto): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.moto = :moto')
            ->andWhere('p.status = :status')
            ->setParameter('moto', $moto)
            ->setParameter('status', Paiement::STATUS_VALIDE)
            ->getQuery()
            ->getResult();
    }

    public function hasAllTaxesPaid(Moto $moto): bool
    {
        $count = $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.taxe)')
            ->andWhere('p.moto = :moto')
            ->andWhere('p.status = :status')
            ->setParameter('moto', $moto)
            ->setParameter('status', Paiement::STATUS_VALIDE)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count === 3; // VIGNETTE, PLAQUE, TCR
    }

    public function getMissingTaxes(Moto $moto): array
    {
        $paidTaxeIds = $this->createQueryBuilder('p')
            ->select('IDENTITY(p.taxe)')
            ->andWhere('p.moto = :moto')
            ->andWhere('p.status = :status')
            ->setParameter('moto', $moto)
            ->setParameter('status', Paiement::STATUS_VALIDE)
            ->getQuery()
            ->getSingleColumnResult();

        $em = $this->getEntityManager();
        $taxeRepo = $em->getRepository(Taxe::class);

        if (empty($paidTaxeIds)) {
            return $taxeRepo->findAll();
        }

        return $taxeRepo->createQueryBuilder('t')
            ->andWhere('t.id NOT IN (:ids)')
            ->setParameter('ids', $paidTaxeIds)
            ->getQuery()
            ->getResult();
    }

    public function getStatistiques(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as total')
            ->addSelect("SUM(CASE WHEN p.status = 'VALIDE' THEN 1 ELSE 0 END) as valides")
            ->addSelect("SUM(CASE WHEN p.status = 'EN_ATTENTE' THEN 1 ELSE 0 END) as enAttente")
            ->addSelect("SUM(CASE WHEN p.status = 'VALIDE' THEN p.montant ELSE 0 END) as montantTotal");

        if ($site) {
            $qb->join('p.moto', 'm')
               ->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getSingleResult();
    }

    public function getStatistiquesByTaxe(?Site $site = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('t.code, t.libelle, COUNT(p.id) as total, SUM(p.montant) as montantTotal')
            ->join('p.taxe', 't')
            ->andWhere('p.status = :status')
            ->setParameter('status', Paiement::STATUS_VALIDE)
            ->groupBy('t.id');

        if ($site) {
            $qb->join('p.moto', 'm')
               ->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getResult();
    }

    public function getRecentPaiements(?Site $site = null, int $limit = 5): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.taxe', 't')
            ->addSelect('t')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit);

        if ($site) {
            $qb->join('p.moto', 'm')
               ->andWhere('m.site = :site')
               ->setParameter('site', $site);
        }

        return $qb->getQuery()->getResult();
    }
}
