<?php

namespace App\Service;

use App\Entity\Dossier;
use App\Entity\Paiement;
use App\Entity\Taxe;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DossierService
{
    public function __construct(
        private EntityManagerInterface $em,
        private AuditService $auditService
    ) {
    }

    public function creerDossier(Dossier $dossier, array $taxes, User $user): Dossier
    {
        $dossier->setCreatedBy($user);
        
        foreach ($taxes as $taxe) {
            if (!$taxe instanceof Taxe) {
                continue;
            }
            
            $paiement = new Paiement();
            $paiement->setMoto($dossier->getMoto());
            $paiement->setTaxe($taxe);
            $paiement->setMontant($taxe->getMontant());
            $paiement->setModePaiement($dossier->getModePaiement());
            $paiement->setCreatedBy($user);
            
            $dossier->addPaiement($paiement);
        }
        
        $dossier->calculerMontantTotal();
        
        $this->em->persist($dossier);
        $this->em->flush();
        
        $this->auditService->logCreate($dossier, $user);
        
        return $dossier;
    }

    public function validerDossier(Dossier $dossier, User $validator): Dossier
    {
        $dossier->valider($validator);
        $this->em->flush();
        
        $this->auditService->logValidation($dossier, $validator);
        
        return $dossier;
    }

    public function rejeterDossier(Dossier $dossier, User $validator, string $motif): Dossier
    {
        $dossier->rejeter($validator, $motif);
        $this->em->flush();
        
        $this->auditService->logUpdate($dossier, $validator, ['motif' => $motif]);
        
        return $dossier;
    }
}
