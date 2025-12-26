<?php

namespace App\Controller\Encoder;

use App\Entity\Motard;
use App\Form\MotardType;
use App\Repository\MotardRepository;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/encoder/motard')]
#[IsGranted('ROLE_ENCODEUR')]
class MotardController extends AbstractController
{
    #[Route('/', name: 'encoder_motard_index', methods: ['GET'])]
    public function index(MotardRepository $motardRepository): Response
    {
        $user = $this->getUser();
        $motards = $this->isGranted('ROLE_ADMIN') 
            ? $motardRepository->findAll()
            : $motardRepository->findByCreator($user);

        return $this->render('encoder/motard/index.html.twig', [
            'motards' => $motards,
        ]);
    }

    #[Route('/new', name: 'encoder_motard_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, AuditService $audit): Response
    {
        $user = $this->getUser();
        $motard = new Motard();
        $form = $this->createForm(MotardType::class, $motard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motard->setCreatedBy($user);
            $em->persist($motard);
            $em->flush();
            $audit->logCreate($motard, $user);
            $this->addFlash('success', 'Motard enregistré avec succès.');
            return $this->redirectToRoute('encoder_motard_index');
        }

        return $this->render('encoder/motard/new.html.twig', [
            'motard' => $motard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'encoder_motard_show', methods: ['GET'])]
    public function show(Motard $motard): Response
    {
        return $this->render('encoder/motard/show.html.twig', [
            'motard' => $motard,
        ]);
    }

    #[Route('/{id}/edit', name: 'encoder_motard_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Motard $motard, EntityManagerInterface $em, AuditService $audit): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(MotardType::class, $motard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $audit->logUpdate($motard, $user);
            $this->addFlash('success', 'Motard modifié avec succès.');
            return $this->redirectToRoute('encoder_motard_index');
        }

        return $this->render('encoder/motard/edit.html.twig', [
            'motard' => $motard,
            'form' => $form,
        ]);
    }
}
