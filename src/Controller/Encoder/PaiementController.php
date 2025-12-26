<?php

namespace App\Controller\Encoder;

use App\Entity\Dossier;
use App\Form\DossierPaiementType;
use App\Repository\DossierRepository;
use App\Service\DossierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/encoder/paiement')]
#[IsGranted('ROLE_ENCODEUR')]
class PaiementController extends AbstractController
{
    #[Route('/', name: 'encoder_paiement_index', methods: ['GET'])]
    public function index(DossierRepository $dossierRepository): Response
    {
        $user = $this->getUser();
        $dossiers = $this->isGranted('ROLE_ADMIN') 
            ? $dossierRepository->findBy([], ['createdAt' => 'DESC'])
            : $dossierRepository->findByCreator($user);

        return $this->render('encoder/paiement/index.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }

    #[Route('/new', name: 'encoder_paiement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DossierService $dossierService): Response
    {
        $user = $this->getUser();
        $dossier = new Dossier();
        $form = $this->createForm(DossierPaiementType::class, $dossier, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taxes = $form->get('taxes')->getData();
            
            if (empty($taxes) || count($taxes) === 0) {
                $this->addFlash('danger', 'Veuillez sélectionner au moins une taxe.');
                return $this->render('encoder/paiement/new.html.twig', [
                    'dossier' => $dossier,
                    'form' => $form,
                ]);
            }
            
            try {
                $dossierService->creerDossier($dossier, $taxes->toArray(), $user);
                $this->addFlash('success', 'Dossier de paiement créé avec succès. Référence: ' . $dossier->getReference());
                return $this->redirectToRoute('encoder_paiement_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('encoder/paiement/new.html.twig', [
            'dossier' => $dossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'encoder_paiement_show', methods: ['GET'])]
    public function show(DossierRepository $dossierRepository, int $id): Response
    {
        $dossier = $dossierRepository->findWithDetails($id);
        
        if (!$dossier) {
            throw $this->createNotFoundException('Dossier non trouvé');
        }

        return $this->render('encoder/paiement/show.html.twig', [
            'dossier' => $dossier,
        ]);
    }
}
