<?php

namespace App\Service;

use App\Entity\AffectationPlaque;
use App\Entity\Moto;
use App\Entity\Plaque;
use App\Entity\User;
use App\Exception\AffectationException;
use App\Repository\PaiementRepository;
use App\Repository\PlaqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PlaqueService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PlaqueRepository $plaqueRepository,
        private PaiementRepository $paiementRepository,
        private AuditService $auditService,
        private LoggerInterface $logger
    ) {}

    public function isEligibleForPlaque(Moto $moto): bool
    {
        if ($moto->hasPlaque()) return false;
        return $this->paiementRepository->hasAllTaxesPaid($moto);
    }

    public function affecterPlaque(Moto $moto, User $validator, ?Plaque $plaque = null): AffectationPlaque
    {
        if ($validator->getSite()->getId() !== $moto->getSite()->getId()) {
            throw new AffectationException('Vous ne pouvez pas affecter une plaque à une moto d\'un autre site');
        }

        if (!$this->isEligibleForPlaque($moto)) {
            throw new AffectationException('Cette moto n\'est pas éligible pour une plaque');
        }

        if ($plaque === null) {
            $plaque = $this->plaqueRepository->findFirstDisponible();
            if ($plaque === null) {
                throw new AffectationException('Aucune plaque disponible');
            }
        }

        if (!$plaque->isDisponible()) {
            throw new AffectationException('Cette plaque n\'est pas disponible');
        }

        $this->em->beginTransaction();
        try {
            $affectation = new AffectationPlaque();
            $affectation->setMoto($moto);
            $affectation->setPlaque($plaque);
            $affectation->setValidatedBy($validator);
            $affectation->setValidatedAt(new \DateTime());

            $plaque->setStatut(Plaque::STATUT_AFFECTEE);

            $this->em->persist($affectation);
            $this->em->flush();
            $this->em->commit();

            $this->auditService->logAffectation($affectation, $validator);
            $this->logger->info('Plaque affectée', ['moto' => $moto->getId(), 'plaque' => $plaque->getNumero()]);

            return $affectation;
        } catch (\Exception $e) {
            $this->em->rollback();
            throw new AffectationException('Erreur: ' . $e->getMessage());
        }
    }

    public function getPlaquesDisponibles(): array
    {
        return $this->plaqueRepository->findDisponibles();
    }
}
