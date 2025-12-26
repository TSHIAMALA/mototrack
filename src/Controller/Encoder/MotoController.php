<?php

namespace App\Controller\Encoder;

use App\Entity\Moto;
use App\Form\MotoType;
use App\Repository\MotoRepository;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/encoder/moto')]
#[IsGranted('ROLE_ENCODEUR')]
class MotoController extends AbstractController
{
    #[Route('/', name: 'encoder_moto_index', methods: ['GET'])]
    public function index(MotoRepository $motoRepository): Response
    {
        $user = $this->getUser();
        $motos = $this->isGranted('ROLE_ADMIN') 
            ? $motoRepository->findAll()
            : $motoRepository->findByCreator($user);

        return $this->render('encoder/moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }

    #[Route('/new', name: 'encoder_moto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, AuditService $audit): Response
    {
        $user = $this->getUser();
        $moto = new Moto();
        $moto->setSite($user->getSite());
        
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moto->setCreatedBy($user);
            $moto->setSite($user->getSite());
            $em->persist($moto);
            $em->flush();
            $audit->logCreate($moto, $user);
            $this->addFlash('success', 'Moto enregistrée avec succès.');
            return $this->redirectToRoute('encoder_moto_index');
        }

        return $this->render('encoder/moto/new.html.twig', [
            'moto' => $moto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'encoder_moto_show', methods: ['GET'])]
    public function show(Moto $moto): Response
    {
        return $this->render('encoder/moto/show.html.twig', [
            'moto' => $moto,
        ]);
    }

    #[Route('/{id}/edit', name: 'encoder_moto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Moto $moto, EntityManagerInterface $em, AuditService $audit): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $audit->logUpdate($moto, $user);
            $this->addFlash('success', 'Moto modifiée avec succès.');
            return $this->redirectToRoute('encoder_moto_index');
        }

        return $this->render('encoder/moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form,
        ]);
    }
}
