<?php

namespace App\Repository;

use App\Entity\Adresse;
use App\Entity\Motard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adresse>
 */
class AdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adresse::class);
    }

    public function findActiveByMotard(Motard $motard): ?Adresse
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.motard = :motard')
            ->andWhere('a.isActive = :active')
            ->setParameter('motard', $motard)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByMotard(Motard $motard): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.motard = :motard')
            ->setParameter('motard', $motard)
            ->orderBy('a.isActive', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function deactivateAllForMotard(Motard $motard): void
    {
        $this->createQueryBuilder('a')
            ->update()
            ->set('a.isActive', ':inactive')
            ->where('a.motard = :motard')
            ->setParameter('inactive', false)
            ->setParameter('motard', $motard)
            ->getQuery()
            ->execute();
    }
}
