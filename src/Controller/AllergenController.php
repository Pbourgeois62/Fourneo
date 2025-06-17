<?php

namespace App\Controller;

use App\Entity\Allergen;
use App\Form\AllergenForm;
use App\Repository\AllergenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/allergen')]
final class AllergenController extends AbstractController
{
    #[Route('', name: 'allergen_index', methods: ['GET'])]
    public function index(AllergenRepository $allergenRepository): Response
    {
        return $this->render('allergen/index.html.twig', [
            'allergens' => $allergenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'allergen_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $allergen = new Allergen();
        $form = $this->createForm(AllergenForm::class, $allergen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($allergen);
            $em->flush();

            $this->addFlash('success', 'Allergène créé avec succès.');
            return $this->redirectToRoute('allergen_index');
        }

        return $this->render('allergen/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'allergen_show', methods: ['GET'])]
    public function show(Allergen $allergen): Response
    {
        return $this->render('allergen/show.html.twig', [
            'allergen' => $allergen,
        ]);
    }

    #[Route('/{id}/edit', name: 'allergen_edit', methods: ['GET', 'POST'])]
    public function edit(Allergen $allergen, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AllergenForm::class, $allergen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Allergène modifié avec succès.');
            return $this->redirectToRoute('allergen_index');
        }

        return $this->render('allergen/edit.html.twig', [
            'form' => $form,
            'allergen' => $allergen,
        ]);
    }

    #[Route('/{id}/delete', name: 'allergen_delete', methods: ['POST'])]
    public function delete(Allergen $allergen, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_allergen_' . $allergen->getId(), $request->request->get('_token'))) {
            $em->remove($allergen);
            $em->flush();
            $this->addFlash('success', 'Allergène supprimé avec succès.');
        }

        return $this->redirectToRoute('allergen_index');
    }
}
