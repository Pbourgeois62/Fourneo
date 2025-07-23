<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/unit')]
class UnitController extends AbstractController
{
    #[Route('/', name: 'unit_index', methods: ['GET'])]
    public function index(UnitRepository $unitRepository): Response
    {
        return $this->render('unit/index.html.twig', [
            'units' => $unitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'unit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unit->setName(strtolower(trim($unit->getName())));

            $entityManager->persist($unit);
            $entityManager->flush();

            $this->addFlash('success', 'Unité créée avec succès !');
            return $this->redirectToRoute('unit_index');
        }

        return $this->render('unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'unit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Unit $unit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unit->setName(strtolower(trim($unit->getName())));
            
            $entityManager->flush();

            $this->addFlash('success', 'Unité modifiée avec succès !');
            return $this->redirectToRoute('unit_index');
        }

        return $this->render('unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'unit_delete', methods: ['POST'])]
    public function delete(Request $request, Unit $unit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $unit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($unit);
            $entityManager->flush();
            $this->addFlash('success', 'Unité supprimée avec succès !');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('unit_index');
    }
}