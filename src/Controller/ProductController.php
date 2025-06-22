<?php

namespace App\Controller;

use App\Entity\Product;
use Psr\Log\LoggerInterface;
use App\Form\AddLabelToProductForm;
use App\Repository\ProductRepository;
use App\Form\AddAllergensToProductForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AddDeliveryNotesToProductForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

#[IsGranted('ROLE_USER')]
#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route('', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function createProduct(): Response
    {
        return $this->render('product/edit.html.twig', [
            'product' => null,
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function editProduct(Product $product): Response
    {
        return $this->render('product/edit.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Product $product, Request $request, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        if ($this->isCsrfTokenValid('delete_product_' . $product->getId(), $request->request->get('_token'))) {
            try {
                $em->remove($product);
                $em->flush();
                $this->addFlash('success', 'Produit supprimé avec succès.');
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Impossible de supprimer ce produit car il est lié à des événements de vente.');
                $logger->error('Failed to delete product due to foreign key constraint: ' . $e->getMessage(), ['product_id' => $product->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur inattendue est survenue lors de la suppression du produit.');
                $logger->error('An unexpected error occurred during product deletion: ' . $e->getMessage(), ['product_id' => $product->getId()]);
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('product_index');
    }

    #[Route('/{id}/add-delivery-note', name: 'add_delivery_note_to_product', methods: ['GET', 'POST'])]
    public function addDeliveryNoteToProduct(EntityManagerInterface $em, Request $request, Product $product): Response
    {
        $form = $this->createForm(AddDeliveryNotesToProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addedDeliveryNotes = $form->get('deliveryNotes')->getData();
            foreach ($addedDeliveryNotes as $deliveryNote) {
                $deliveryNote->addProduct($product);
                $em->persist($deliveryNote);            
            }
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Bons de livraison liés avec succès.');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('delivery_note/link.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/{id}/add-label', name: 'add_label_to_product', methods: ['GET', 'POST'])]
    public function addLabelToProduct(EntityManagerInterface $em, Request $request, Product $product): Response
    {
        $form = $this->createForm(AddLabelToProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addedLabels = $form->get('labels')->getData();
            foreach ($addedLabels as $label) {
                $label->setProduct($product);
                $em->persist($label);            
            }
            $em->persist($product);
            $em->flush();          
            $this->addFlash('success', 'Étiquettes liées avec succès.');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }           
        return $this->render('label/link.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/add-allergen', name: 'add_allergen_to_product', methods: ['GET', 'POST'])]
    public function addAllergenToProduct(EntityManagerInterface $em, Request $request, Product $product): Response
    {
        $form = $this->createForm(AddAllergensToProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addedAllergens = $form->get('allergens')->getData();
            foreach ($addedAllergens as $allergen) {
                $allergen->addProduct($product);
                $em->persist($allergen);
            }          
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Allergènes liés avec succès.');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('allergen/link.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }
}
