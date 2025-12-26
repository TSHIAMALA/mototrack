<?php

namespace App\Service;

use App\Entity\Paiement;
use App\Entity\User;
use App\Exception\PaiementException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PaiementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private AuditService $auditService,
        private LoggerInterface $logger
    ) {}

    public function enregistrerPaiement(Paiement $paiement, User $user): Paiement
    {
        $paiement->setCreatedBy($user);
        $paiement->setStatus(Paiement::STATUS_EN_ATTENTE);

        $this->em->persist($paiement);
        $this->em->flush();

        $this->auditService->logCreate($paiement, $user);
        $this->logger->info('Paiement enregistré', ['id' => $paiement->getId()]);

        return $paiement;
    }

    public function validerPaiement(Paiement $paiement, User $validator): Paiement
    {
        $motoSite = $paiement->getMoto()->getSite();
        $validatorSite = $validator->getSite();

        if ($motoSite->getId() !== $validatorSite->getId()) {
            throw new PaiementException('Vous ne pouvez pas valider un paiement d\'un autre site');
        }

        if (!$paiement->isEnAttente()) {
            throw new PaiementException('Ce paiement a déjà été validé');
        }

        $paiement->valider($validator);
        $this->em->flush();

        $this->auditService->logValidation($paiement, $validator);
        $this->logger->info('Paiement validé', ['id' => $paiement->getId()]);

        return $paiement;
    }
}
