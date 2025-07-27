<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Service\RecipeCostCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/recipe')]
final class RecipeController extends AbstractController
{
    public function __construct(private RecipeCostCalculator $recipeCostCalculator) {}   

    #[Route('/new', name: 'recipe_new', methods: ['GET'])]
    public function newRecipe(): Response
    {
        $recipe = new Recipe();

        return $this->render('recipe/add_steps.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/{id}', name: 'recipe_show', requirements: ['id' => '\d+'])]
    public function show(Recipe $recipe): Response
    {
        $totalCost = $this->recipeCostCalculator->calculateTotalCost($recipe);

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'totalCost' => $totalCost,
        ]);
    }

    #[Route('/{id}/edit', name: 'recipe_edit', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function editRecipe(Recipe $recipe): Response
    {
        return $this->render('recipe/add_steps.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/delete/{id}', name: 'recipe_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteRecipe(Request $request, Recipe $recipe, EntityManagerInterface $em): Response    {
        
        if ($this->isCsrfTokenValid('delete_recipe_' . $recipe->getId(), $request->request->get('_token'))) {
            $em->remove($recipe);
            $em->flush();

            $this->addFlash('success', 'Recette supprimée avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('product_index');
    }    
}
