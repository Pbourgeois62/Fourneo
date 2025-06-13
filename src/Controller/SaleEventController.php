<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SaleEvent;
use App\Entity\ProductEvent;
use App\Form\SaleEventTypeForm;
use App\Repository\SaleEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SaleEventAddProductsTypeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/sale-event')]
final class SaleEventController extends AbstractController
{
    #[Route('', name: 'sale_event_index', methods: ['GET'])]
    public function index(SaleEventRepository $saleEventRepository): Response
    {
        return $this->render('sale_event/index.html.twig', [
            'sale_events' => $saleEventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'sale_event_new', methods: ['GET', 'POST'])]
    public function createEvent(Request $request, EntityManagerInterface $em): Response
    {
        // $saleEvent = new SaleEvent();
        // $form = $this->createForm(SaleEventTypeForm::class);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $em->persist($saleEvent);
        //     $em->flush();

        //     $this->addFlash('success', 'Événement de vente créé avec succès.');
        //     return $this->redirectToRoute('sale_event_add_products', ['id' => $saleEvent->getId()]);
        // }

        return $this->render('sale_event/edit.html.twig', [
            // 'form' => $form,
        ]);
    }

    // #[Route('/{id}/add-products', name: 'sale_event_add_products', methods: ['GET', 'POST'])]
    // public function addProducts(Request $request, EntityManagerInterface $em, SaleEvent $saleEvent): Response
    // {
    //     $form = $this->createForm(SaleEventAddProductsTypeForm::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         /** @var Product[] $products */
    //         $products = $form->get('products')->getData();

    //         foreach ($products as $product) {
    //             $productEvent = new ProductEvent();
    //             $productEvent->setEvent($saleEvent);
    //             $productEvent->setProduct($product);
    //             $em->persist($productEvent);
    //         }

    //         $em->flush();

    //         $this->addFlash('success', 'Produits ajoutés avec succès.');
    //         return $this->redirectToRoute('sale_event_index');
    //     }

    //     return $this->render('sale_event/add_products.html.twig', [
    //         'form' => $form->createView(),
    //         'saleEvent' => $saleEvent,
    //     ]);
    // }


    #[Route('/{id}', name: 'sale_event_show', methods: ['GET'])]
    public function show(SaleEvent $saleEvent): Response
    {
        return $this->render('sale_event/show.html.twig', [
            'sale_event' => $saleEvent,
        ]);
    }

    #[Route('/{id}/edit', name: 'sale_event_edit', methods: ['GET', 'POST'])]
    public function edit(SaleEvent $saleEvent, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SaleEventTypeForm::class, $saleEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Événement de vente modifié avec succès.');
            return $this->redirectToRoute('sale_event_index');
        }

        return $this->render('sale_event/edit.html.twig', [
            'form' => $form,
            'sale_event' => $saleEvent,
        ]);
    }

    #[Route('/{id}/delete', name: 'sale_event_delete', methods: ['POST'])]
    public function delete(SaleEvent $saleEvent, Request $request, EntityManagerInterface $em): Response
    {        
        if ($this->isCsrfTokenValid('delete_sale_event_' . $saleEvent->getId(), $request->request->get('_token'))) {
            // dd($saleEvent);
            $em->remove($saleEvent);
            $em->flush();
            $this->addFlash('success', 'Événement de vente supprimé avec succès.');
        }

        return $this->redirectToRoute('sale_event_index');
    }
}
