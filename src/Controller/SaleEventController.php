<?php

namespace App\Controller;

use App\Entity\SaleEvent;
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
        return $this->render('sale_event/index.html.twig', [
            'sale_events' => $saleEventRepository->findBy([], ['startDate' => 'ASC']),
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

    #[Route('/{id}', name: 'sale_event_show', methods: ['GET'])]
    public function show(SaleEvent $saleEvent): Response
    {
        return $this->render('sale_event/show.html.twig', [
            'sale_event' => $saleEvent,
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
}
