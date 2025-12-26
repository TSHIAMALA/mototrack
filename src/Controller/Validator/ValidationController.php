<?php

namespace App\Controller\Validator;

use App\Entity\Dossier;
use App\Entity\Moto;
use App\Repository\DossierRepository;
use App\Repository\MotoRepository;
use App\Repository\PlaqueRepository;
use App\Service\DossierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/validator')]
#[IsGranted('ROLE_VALIDATOR')]
class ValidationController extends AbstractController
{
    #[Route('/paiements', name: 'validator_paiements', methods: ['GET'])]
    public function paiements(DossierRepository $dossierRepository): Response
    {
        $dossiers = $dossierRepository->findEnAttente();

        return $this->render('validator/paiements.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }

    #[Route('/dossier/{id}', name: 'validator_dossier_show', methods: ['GET'])]
    public function showDossier(DossierRepository $dossierRepository, int $id): Response
    {
        $dossier = $dossierRepository->findWithDetails($id);
        
        if (!$dossier) {
            throw $this->createNotFoundException('Dossier non trouvé');
        }

        return $this->render('validator/dossier_show.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    #[Route('/dossier/{id}/valider', name: 'validator_dossier_valider', methods: ['POST'])]
    public function validerDossier(Request $request, Dossier $dossier, DossierService $dossierService): Response
    {
        if ($this->isCsrfTokenValid('valider'.$dossier->getId(), $request->request->get('_token'))) {
            $dossierService->validerDossier($dossier, $this->getUser());
            $this->addFlash('success', 'Dossier ' . $dossier->getReference() . ' validé avec succès.');
        }

        return $this->redirectToRoute('validator_paiements');
    }

    #[Route('/dossier/{id}/rejeter', name: 'validator_dossier_rejeter', methods: ['POST'])]
    public function rejeterDossier(Request $request, Dossier $dossier, DossierService $dossierService): Response
    {
        if ($this->isCsrfTokenValid('rejeter'.$dossier->getId(), $request->request->get('_token'))) {
            $motif = $request->request->get('motif', 'Non spécifié');
            $dossierService->rejeterDossier($dossier, $this->getUser(), $motif);
            $this->addFlash('warning', 'Dossier ' . $dossier->getReference() . ' rejeté.');
        }

        return $this->redirectToRoute('validator_paiements');
    }

    #[Route('/plaques', name: 'validator_plaques', methods: ['GET'])]
    public function plaques(MotoRepository $motoRepository, PlaqueRepository $plaqueRepository): Response
    {
        $user = $this->getUser();
        
        // On utilise toujours la méthode sécurisée qui vérifie le dossier validé
        // Si admin, on pourrait vouloir voir tout, mais pour l'affectation, restons stricts
        $site = $user->getSite();
        
        if ($this->isGranted('ROLE_ADMIN') && !$site) {
             // Cas limite pour l'admin global sans site, à gérer si besoin
             // Pour l'instant on retourne vide ou on force la sélection d'un site
             $motos = []; 
        } else {
            $motos = $motoRepository->findEligibleForPlaque($site);
        }
        
        $plaquesDisponibles = $plaqueRepository->findDisponibles();

        return $this->render('validator/plaques.html.twig', [
            'motos' => $motos,
            'plaques' => $plaquesDisponibles,
        ]);
    }

    #[Route('/plaque/{id}/affecter', name: 'validator_plaque_affecter', methods: ['POST'])]
    public function affecterPlaque(Request $request, Moto $moto, PlaqueRepository $plaqueRepository, EntityManagerInterface $em, DossierRepository $dossierRepository): Response
    {
        $plaqueId = $request->request->get('plaque_id');
        
        if ($this->isCsrfTokenValid('affecter'.$moto->getId(), $request->request->get('_token')) && $plaqueId) {
            
            // VERIFICATION DE SECURITE SUPPLEMENTAIRE
            $dossier = $dossierRepository->findOneBy(['moto' => $moto, 'status' => Dossier::STATUS_VALIDE]);
            
            if (!$dossier) {
                $this->addFlash('danger', 'Impossible d\'affecter une plaque : Aucun dossier validé trouvé pour cette moto.');
                return $this->redirectToRoute('validator_plaques');
            }

            $plaque = $plaqueRepository->find($plaqueId);
            if ($plaque && $plaque->getStatut() === 'DISPONIBLE') {
                $affectation = new \App\Entity\AffectationPlaque();
                $affectation->setMoto($moto);
                $affectation->setPlaque($plaque);
                $affectation->setValidatedAt(new \DateTime());
                $affectation->setValidatedBy($this->getUser());
                
                $plaque->setStatut('AFFECTEE');
                
                $em->persist($affectation);
                $em->flush();
                
                $this->addFlash('success', sprintf('Plaque %s affectée à la moto.', $plaque->getNumero()));
            }
        }

        return $this->redirectToRoute('validator_plaques');
    }
}
