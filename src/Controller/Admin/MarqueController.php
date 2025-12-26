<?php

namespace App\Controller\Admin;

use App\Entity\MarqueMoto;
use App\Form\MarqueMotoType;
use App\Repository\MarqueMotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/marque')]
#[IsGranted('ROLE_ADMIN')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'admin_marque_index', methods: ['GET'])]
    public function index(MarqueMotoRepository $marqueRepository): Response
    {
        return $this->render('admin/marque/index.html.twig', [
            'marques' => $marqueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_marque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $marque = new MarqueMoto();
        $form = $this->createForm(MarqueMotoType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($marque);
            $em->flush();
            $this->addFlash('success', 'Marque créée avec succès.');
            return $this->redirectToRoute('admin_marque_index');
        }

        return $this->render('admin/marque/new.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_marque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarqueMoto $marque, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MarqueMotoType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Marque modifiée avec succès.');
            return $this->redirectToRoute('admin_marque_index');
        }

        return $this->render('admin/marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_marque_toggle', methods: ['POST'])]
    public function toggle(Request $request, MarqueMoto $marque, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle'.$marque->getId(), $request->request->get('_token'))) {
            $marque->setIsActive(!$marque->isActive());
            $em->flush();
            $this->addFlash('success', 'Statut de la marque modifié.');
        }
        return $this->redirectToRoute('admin_marque_index');
    }
}
