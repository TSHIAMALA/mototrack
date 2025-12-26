<?php

namespace App\Repository;

use App\Entity\Site;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findBySite(Site $site): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.site = :site')
            ->setParameter('site', $site)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countByRole(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT 
                    CASE 
                        WHEN roles LIKE '%ROLE_ADMIN%' THEN 'Admin'
                        WHEN roles LIKE '%ROLE_VALIDATOR%' THEN 'Validateur'
                        WHEN roles LIKE '%ROLE_ENCODEUR%' THEN 'Encodeur'
                        ELSE 'Autre'
                    END as role_name,
                    COUNT(*) as total
                FROM utilisateur
                WHERE is_active = 1
                GROUP BY role_name";
        
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }
}
