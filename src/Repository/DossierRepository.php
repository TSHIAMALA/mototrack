<?php

namespace App\Repository;

use App\Entity\Dossier;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dossier::class);
    }

    public function findByCreator(User $user): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEnAttente(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.status = :status')
            ->setParameter('status', Dossier::STATUS_EN_ATTENTE)
            ->orderBy('d.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countEnAttente(): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.status = :status')
            ->setParameter('status', Dossier::STATUS_EN_ATTENTE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findWithDetails(int $id): ?Dossier
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.moto', 'm')->addSelect('m')
            ->leftJoin('m.motard', 'mo')->addSelect('mo')
            ->leftJoin('mo.adresses', 'a')->addSelect('a')
            ->leftJoin('d.paiements', 'p')->addSelect('p')
            ->leftJoin('p.taxe', 't')->addSelect('t')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
