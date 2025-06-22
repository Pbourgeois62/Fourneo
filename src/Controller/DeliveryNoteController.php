<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\DeliveryNote;
use App\Form\DeliveryNoteTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DeliveryNoteRepository;
use App\Form\AddDeliveryNotesToProductForm;
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

    #[Route('/new', name: 'delivery_note_new', methods: ['GET', 'POST'])]
    #[Route('/new/{product}', name: 'delivery_note_new_for_product', methods: ['GET', 'POST'])]
    public function createDeliveryNote(
        Request $request,
        EntityManagerInterface $em,
        ?Product $product
    ): Response {
        $deliveryNote = new DeliveryNote();
        if ($product) {
            $deliveryNote->addProduct($product);
        }

        $form = $this->createForm(DeliveryNoteTypeForm::class, $deliveryNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($deliveryNote);
            $em->flush();

            $this->addFlash('success', 'Bon de livraison créé avec succès.');

            if ($product) {               
                return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
            }

            return $this->redirectToRoute('delivery_note_index');
        }

        return $this->render('delivery_note/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/{id}', name: 'delivery_note_show', methods: ['GET'])]
    public function showDeliveryNote(DeliveryNote $deliveryNote): Response
    {
        return $this->render('delivery_note/show.html.twig', [
            'deliveryNote' => $deliveryNote,
        ]);
    }

    #[Route('/{id}/edit', name: 'delivery_note_edit', methods: ['GET', 'POST'])]
    public function editDeliveryNote(DeliveryNote $deliveryNote, Request $request, EntityManagerInterface $em): Response
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
    public function deleteDeliveryNote(DeliveryNote $deliveryNote, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_delivery_note_' . $deliveryNote->getId(), $request->request->get('_token'))) {
            $em->remove($deliveryNote);
            $em->flush();
            $this->addFlash('success', 'Bon de livraison supprimé avec succès.');
        }

        return $this->redirectToRoute('delivery_note_index');
    }
}
