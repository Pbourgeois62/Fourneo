<?php

namespace App\Controller;

use App\Entity\SaleEvent;
use App\Entity\ProductEvent;
use App\Repository\SaleEventRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $today = new \DateTimeImmutable();
        $incomingEvents = $saleEventRepository->findIncomingEvents();
        $eventsOfTheDay = $saleEventRepository->findEventsForSpecificDate($today);
        $closedEvents = $saleEventRepository->findClosedEvents();
        return $this->render('sale_event/index.html.twig', [
            'incomingEvents' => $incomingEvents,
            'eventsOfTheDay' => $eventsOfTheDay,
            'closedEvents' => $closedEvents
        ]);
    }

    #[Route('/new', name: 'sale_event_new', methods: ['GET', 'POST'])]
    public function createEvent(): Response
    {
        return $this->render('sale_event/edit.html.twig', ['saleEvent' => null,]);
    }

    #[Route('/{id}/edit', name: 'sale_event_edit', methods: ['GET', 'POST'])]
    public function editEvent(?SaleEvent $saleEvent): Response
    {
        return $this->render('sale_event/edit.html.twig', [
            'saleEvent' => $saleEvent
        ]);
    }

    #[Route('/{id}', name: 'sale_event_show', methods: ['GET', 'POST'])]
    public function manageSaleEven(SaleEvent $saleEvent): Response
    {
        return $this->render('sale_event/show.html.twig', [
            'saleEvent' => $saleEvent,
        ]);
    }

    #[Route('/{id}/delete', name: 'sale_event_delete', methods: ['POST'])]
    public function delete(SaleEvent $saleEvent, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_sale_event_' . $saleEvent->getId(), $request->request->get('_token'))) {
            $em->remove($saleEvent);
            $em->flush();
            $this->addFlash('success', 'Événement de vente supprimé avec succès.');
        }
        return $this->redirectToRoute('sale_event_index');
    }

    #[Route('/{id}/show-resume', name: 'sale_event_show_resume', methods: ['GET', 'POST'])]
    public function showResume(SaleEvent $saleEvent): Response
    {
        return $this->render('sale_event/resume.html.twig', [
            'saleEvent' => $saleEvent,
        ]);
    }

    // #[Route('/{id}/product/out-of-stock', name: 'sale_event_product_event_out_of_stock')]
    // public function markProductEventOutOfStock(
    //     ProductEvent $productEvent,
    //     EntityManagerInterface $entityManager
    // ): Response {

    //     $productEvent->markAsOutOfStockForEvent();
    //     $productEvent->setUnsoldPrice(0);

    //     $saleEventId = $productEvent->getEvent()->getId();

    //     $entityManager->flush();

    //     $this->addFlash('success', sprintf(
    //         'Le produit "%s" a été marqué comme en rupture de stock pour cet événement.',
    //         $productEvent->getProduct()->getName()
    //     ));

    //     return $this->redirectToRoute('sale_event_show', ['id' => $saleEventId]);
    // }
}
