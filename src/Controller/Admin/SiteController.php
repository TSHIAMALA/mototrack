<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/site')]
#[IsGranted('ROLE_ADMIN')]
class SiteController extends AbstractController
{
    #[Route('/', name: 'admin_site_index', methods: ['GET'])]
    public function index(SiteRepository $siteRepository): Response
    {
        return $this->render('admin/site/index.html.twig', [
            'sites' => $siteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_site_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();
            $this->addFlash('success', 'Site créé avec succès.');
            return $this->redirectToRoute('admin_site_index');
        }

        return $this->render('admin/site/new.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Site $site, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Site modifié avec succès.');
            return $this->redirectToRoute('admin_site_index');
        }

        return $this->render('admin/site/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_site_toggle', methods: ['POST'])]
    public function toggle(Request $request, Site $site, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle'.$site->getId(), $request->request->get('_token'))) {
            $site->setIsActive(!$site->isActive());
            $em->flush();
            $this->addFlash('success', 'Statut du site modifié.');
        }
        return $this->redirectToRoute('admin_site_index');
    }
}
