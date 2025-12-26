<?php

namespace App\Service;

use App\Entity\AffectationPlaque;
use App\Entity\AuditLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AuditService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack
    ) {}

    public function logCreate(object $entity, User $user): void
    {
        $this->log($entity, 'CREATE', $user, null, $this->extractData($entity));
    }

    public function logUpdate(object $entity, User $user, ?array $oldData = null): void
    {
        $this->log($entity, 'UPDATE', $user, $oldData, $this->extractData($entity));
    }

    public function logValidation(object $entity, User $user): void
    {
        $this->log($entity, 'VALIDATE', $user, null, $this->extractData($entity));
    }

    public function logAffectation(AffectationPlaque $affectation, User $user): void
    {
        $data = [
            'moto_id' => $affectation->getMoto()->getId(),
            'plaque_numero' => $affectation->getPlaque()->getNumero(),
        ];
        $this->log($affectation, 'AFFECTATION', $user, null, $data);
    }

    private function log(object $entity, string $action, User $user, ?array $oldData, ?array $newData): void
    {
        $audit = new AuditLog();
        $audit->setEntityType($this->getEntityType($entity));
        $audit->setEntityId($this->getEntityId($entity));
        $audit->setAction($action);
        $audit->setOldData($oldData);
        $audit->setNewData($newData);
        $audit->setUser($user);
        $audit->setSite($user->getSite());
        $audit->setIpAddress($this->requestStack->getCurrentRequest()?->getClientIp());

        $this->em->persist($audit);
        $this->em->flush();
    }

    private function getEntityType(object $entity): string
    {
        return substr(get_class($entity), strrpos(get_class($entity), '\\') + 1);
    }

    private function getEntityId(object $entity): int
    {
        return method_exists($entity, 'getId') ? ($entity->getId() ?? 0) : 0;
    }

    private function extractData(object $entity): array
    {
        $data = [];
        if (method_exists($entity, 'getId')) $data['id'] = $entity->getId();
        return $data;
    }
}
