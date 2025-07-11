<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\StepForm;
use App\Form\RecipeForm;
use App\Entity\RecipeProduct;
use App\Entity\RecipeProducts;
use App\Repository\RecipeRepository;
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
    #[Route('/index', name: 'recipe_index', methods: ['GET'])]
    public function indexRecipes(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'recipe_show', methods: ['GET'])]
    public function showRecipe(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/new', name: 'recipe_new', methods: ['GET'])]
    public function newRecipe(): Response
    {        
       return $this->render('recipe/edit.html.twig', [ 
            'recipe' => null
        ]);
    }

    #[Route('/{id}/edit', name: 'recipe_edit', methods: ['GET'])]
    public function editRecipe(Recipe $recipe): Response
    {
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/{id}', name: 'recipe_delete', methods: ['POST'])]
    public function deleteRecipe(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_recipe_' . $recipe->getId(), $request->request->get('_token'))) {
            $em->remove($recipe);
            $em->flush();

            $this->addFlash('success', 'Recette supprimée avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('recipe_index');
    }

    #[Route('/{id}/add_steps', name: 'recipe_add_steps', methods: ['GET'])]
    public function addStep(Recipe $recipe): Response
    {
        return $this->render('recipe/add_steps.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
