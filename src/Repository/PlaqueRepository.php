<?php

namespace App\Repository;

use App\Entity\Plaque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plaque>
 */
class PlaqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plaque::class);
    }

    public function findDisponibles(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.statut = :statut')
            ->setParameter('statut', Plaque::STATUT_DISPONIBLE)
            ->orderBy('p.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFirstDisponible(): ?Plaque
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.statut = :statut')
            ->setParameter('statut', Plaque::STATUT_DISPONIBLE)
            ->orderBy('p.numero', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByStatut(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.statut, COUNT(p.id) as total')
            ->groupBy('p.statut')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiques(): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as total')
            ->addSelect("SUM(CASE WHEN p.statut = 'DISPONIBLE' THEN 1 ELSE 0 END) as disponibles")
            ->addSelect("SUM(CASE WHEN p.statut = 'AFFECTEE' THEN 1 ELSE 0 END) as affectees")
            ->getQuery()
            ->getSingleResult();

        return $result;
    }

    public function searchByNumero(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.numero LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.numero', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function getStatistiquesByStatut(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.statut, COUNT(p.id) as total')
            ->groupBy('p.statut')
            ->getQuery()
            ->getResult();
    }
}
