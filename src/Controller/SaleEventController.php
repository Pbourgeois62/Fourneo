<?php

namespace App\Controller;

use App\Entity\SaleEvent;
use App\Service\AllergenCollector;
use App\Service\RecipeAnalyzer;
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

    #[Route('/{id}/show', name: 'sale_event_show', methods: ['GET', 'POST'])]
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

    #[Route('/{id}/resume', name: 'sale_event_show_resume', methods: ['GET', 'POST'])]
    public function showResume(SaleEvent $saleEvent): Response
    {
        return $this->render('sale_event/resume.html.twig', [
            'saleEvent' => $saleEvent,
        ]);
    }

    #[Route('/{id}/plan', name: 'sale_event_plan')]
    public function plan(SaleEvent $saleEvent, AllergenCollector $allergenCollector, RecipeAnalyzer $recipeAnalyzer): Response
    {
        $productsInSaleView = [];
        $totalQuantity = 0;
        $allProductsForAnalysis = [];
        $recipesToAnalyze = [];

        foreach ($saleEvent->getProductEvents() as $productEvent) {
            $product = $productEvent->getProduct();
            $quantitySold = $productEvent->getQuantity();
            $recipe = $product?->getRecipe();

            if ($product) {
                $allProductsForAnalysis[] = $product;
            }

            if ($recipe) {
                $recipesToAnalyze[] = $recipe;
            }

            $productsInSaleView[] = [
                'name' => $product?->getName() ?? 'Produit inconnu',
                'quantity' => $quantitySold,
                'duration' => $recipe?->getTotalDuration() ?? 0,
            ];

            $totalQuantity += $quantitySold;
        }

        $totalIngredients = [];
        foreach ($saleEvent->getProductEvents() as $productEvent) {
            $product = $productEvent->getProduct();
            $quantitySold = $productEvent->getQuantity();
            $recipe = $product?->getRecipe();

            if ($recipe) {
                $ingredientsForProduct = $recipeAnalyzer->calculateTotalIngredients($recipe, $quantitySold);
                foreach ($ingredientsForProduct as $ingredientId => $data) {
                    if (!isset($totalIngredients[$ingredientId])) {
                        $totalIngredients[$ingredientId] = $data;
                    } else {
                        $totalIngredients[$ingredientId]['quantity'] += $data['quantity'];
                    }
                }
            }
        }

        $totalProductionDuration = $recipeAnalyzer->calculateTotalProductionDuration($recipesToAnalyze);
        $uniqueAllergensMap = $allergenCollector->collectAllergensForProducts($allProductsForAnalysis);
        $uniqueAllergenNames = array_values($uniqueAllergensMap);

        return $this->render('sale_event/plan.html.twig', [
            'saleEvent' => $saleEvent,
            'products' => $productsInSaleView,
            'totalQuantity' => $totalQuantity,
            'totalIngredients' => $totalIngredients,
            'totalDuration' => $totalProductionDuration,
            'uniqueAllergens' => $uniqueAllergenNames
        ]);
    }
}