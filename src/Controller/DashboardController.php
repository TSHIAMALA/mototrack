<?php

namespace App\Controller;

use App\Repository\AffectationPlaqueRepository;
use App\Repository\MotardRepository;
use App\Repository\MotoRepository;
use App\Repository\PaiementRepository;
use App\Repository\PlaqueRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        MotoRepository $motoRepository,
        MotardRepository $motardRepository,
        PlaqueRepository $plaqueRepository,
        PaiementRepository $paiementRepository,
        AffectationPlaqueRepository $affectationRepository,
        SiteRepository $siteRepository,
        UserRepository $userRepository,
        \App\Repository\DossierRepository $dossierRepository
    ): Response {
        $user = $this->getUser();
        $site = $user->getSite();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $siteFilter = $isAdmin ? null : $site;

        // Statistiques principales
        $stats = [
            'motos' => $motoRepository->getStatistiques($siteFilter),
            'motards' => $motardRepository->getStatistiques($siteFilter),
            'plaques' => $plaqueRepository->getStatistiques(),
            // Modification: On compte les dossiers au lieu des paiements individuels
            'paiements' => [
                'total' => $dossierRepository->count([]), // Total dossiers
                'montant' => $dossierRepository->getMontantTotal($siteFilter), // Montant total des dossiers
                'today' => $dossierRepository->countToday($siteFilter) // Dossiers aujourd'hui
            ],
            'dossiers_attente' => $dossierRepository->count(['status' => \App\Entity\Dossier::STATUS_EN_ATTENTE]),
            'plaques_dispo' => $plaqueRepository->count(['statut' => 'DISPONIBLE']),
        ];

        // Données pour les graphiques
        $chartData = [];
        
        // Paiements par taxe (pour camembert) - On garde ça car c'est utile pour l'analyse
        $paiementsByTaxe = $paiementRepository->getStatistiquesByTaxe($siteFilter);
        $chartData['paiementsByTaxe'] = $paiementsByTaxe;

        // Motos par site (pour bar chart) - admin seulement
        if ($isAdmin) {
            $chartData['motosBySite'] = $motoRepository->countBySite();
            $stats['sites'] = ['total' => count($siteRepository->findAll())];
            $stats['users'] = ['total' => count($userRepository->findAll())];
        }

        // Statuts des plaques (pour donut)
        $chartData['plaquesByStatut'] = $plaqueRepository->countByStatut();

        // Dernières affectations
        $recentAffectations = $affectationRepository->getRecentAffectations($siteFilter, 5);

        // Derniers dossiers (au lieu de paiements)
        $recentDossiers = $dossierRepository->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'chartData' => $chartData,
            'recentAffectations' => $recentAffectations,
            'recentDossiers' => $recentDossiers, // Renamed variable
            'isAdmin' => $isAdmin,
        ]);
    }
}
