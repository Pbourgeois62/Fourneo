<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\DeliveryNote;
use App\Form\DeliveryNoteTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DeliveryNoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/delivery-note')]
final class DeliveryNoteController extends AbstractController
{
    #[Route('', name: 'delivery_note_index', methods: ['GET'])]
    public function index(DeliveryNoteRepository $deliveryNoteRepository): Response
    {
        return $this->render('delivery_note/index.html.twig', [
            'deliveryNotes' => $deliveryNoteRepository->findAll(),
        ]);
    }

    #[Route('/{id}/link', name: 'delivery_note_link', methods: ['GET', 'POST'])]
    public function link(Product $product): Response
    {
        return $this->render('delivery_note/link.html.twig', ['product' => $product]);
    }

    #[Route('/new', name: 'delivery_note_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $deliveryNote = new DeliveryNote();
        $form = $this->createForm(DeliveryNoteTypeForm::class, $deliveryNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($deliveryNote);
            $em->flush();

            $this->addFlash('success', 'Bon de livraison créé avec succès.');
            return $this->redirectToRoute('delivery_note_index');
        }

        return $this->render('delivery_note/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delivery_note_show', methods: ['GET'])]
    public function show(DeliveryNote $deliveryNote): Response
    {
        return $this->render('delivery_note/show.html.twig', [
            'deliveryNote' => $deliveryNote,
        ]);
    }

    #[Route('/{id}/edit', name: 'delivery_note_edit', methods: ['GET', 'POST'])]
    public function edit(DeliveryNote $deliveryNote, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DeliveryNoteTypeForm::class, $deliveryNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Bon de livraison modifié avec succès.');
            return $this->redirectToRoute('delivery_note_index');
        }

        return $this->render('delivery_note/edit.html.twig', [
            'form' => $form,
            'deliveryNote' => $deliveryNote,
        ]);
    }

    #[Route('/{id}/delete', name: 'delivery_note_delete', methods: ['POST'])]
    public function delete(DeliveryNote $deliveryNote, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_delivery_note_' . $deliveryNote->getId(), $request->request->get('_token'))) {
            $em->remove($deliveryNote);
            $em->flush();
            $this->addFlash('success', 'Bon de livraison supprimé avec succès.');
        }

        return $this->redirectToRoute('delivery_note_index');
    }
}
